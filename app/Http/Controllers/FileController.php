<?php

namespace App\Http\Controllers;

use App\Models\FileDocument;
use App\Models\User;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class FileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $user = $request->user();
        $q = FileDocument::query();

        // Non-admins: restrict to files that belong to them or uploaded by them.
        if (!($user && method_exists($user, 'hasRole') && $user->hasRole('admin'))) {
            if ($user && $user->id) {
                $q->where(function($sub) use ($user) {
                    $sub->where('user_id', $user->id)
                        ->orWhere('created_by', $user->id);
                });
            }
        }

        // Filters from AJAX
        if ($request->filled('project_id')) {
            $q->where('project_id', (int) $request->project_id);
        }
        if ($request->filled('user_id')) {
            $q->where('user_id', (int) $request->user_id);
        }

        if ($request->filled('category') && $request->category !== 'all') {
            $cat = strtolower($request->category);
            $q->where(function($sub) use ($cat) {
                $sub->where('category', $cat);

                if ($cat === 'pdf') {
                    $sub->orWhere('mime', 'like', '%pdf%');
                } elseif ($cat === 'image') {
                    $sub->orWhere('mime', 'like', 'image/%');
                } elseif ($cat === 'design') {
                    $sub->orWhere('mime', 'like', '%dwg%')
                        ->orWhere('path', 'like', '%.dwg')
                        ->orWhere('path', 'like', '%.skp')
                        ->orWhere('path', 'like', '%.dxf');
                } elseif ($cat === 'document') {
                    $sub->orWhere('mime', 'like', '%officedocument%')
                        ->orWhere('mime', 'like', '%msword%')
                        ->orWhere('mime', 'like', '%excel%')
                        ->orWhere('mime', 'like', '%text%');
                } else {
                    $sub->orWhere('mime', 'like', '%'.$cat.'%');
                }
            });
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $q->where('name', 'like', '%'.$s.'%');
        }

        $q->with(['uploader','customer','project'])->orderBy('created_at','desc');

        $perPage = 20;
        $files = $q->paginate($perPage)->appends($request->query());

        // transform collection to a safe, small structure
        $files->getCollection()->transform(function(FileDocument $f) {
            return [
                'id' => $f->id,
                'user_id' => $f->user_id,
                'project_id' => $f->project_id,
                'name' => $f->name,
                'path' => $f->path,
                'mime' => $f->mime,
                'size' => $f->size,
                'category' => $f->category,
                'created_at' => $f->created_at,
                'image_url' => ($f->mime && str_starts_with($f->mime, 'image/')) ? route('admin.files.image', ['file' => $f->id]) : null,
                'download_url' => route('admin.files.download', ['file' => $f->id]),
                'uploader' => $f->uploader ? ['id'=>$f->uploader->id,'name'=>$f->uploader->name ?? $f->uploader->email] : null,
                'customer' => $f->customer ? ['id'=>$f->customer->id,'name'=>$f->customer->name] : null,
                'project' => $f->project ? ['id'=>$f->project->id,'name'=>$f->project->name] : null,
            ];
        });

         // ✅ If request is AJAX → return JSON
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json($files);
        }

        $user = auth()->user();

        if ($user && $user->role === 'admin') {
            $customers = User::where('role', 'customer')->get();
            $projects  = Project::all();
        } else {
            $customers = $user ? collect([$user]) : collect();
            $projects  = $user ? Project::where('user_id', $user->id)->get() : collect();
        }

        return view('admin.file.index', compact('customers','projects'));
    }

    // GET /admin/files/create
    public function create()
    {
        $user = auth()->user();

        if ($user && $user->role === 'admin') {
            $customers = User::where('role', 'customer')->get();
            $projects  = Project::all();
        } else {
            $customers = $user ? collect([$user]) : collect();
            $projects  = $user ? Project::where('user_id', $user->id)->get() : collect();
        }

        return view('admin.file.create', compact('customers', 'projects'));
    }

    // POST /admin/files (AJAX upload)
    public function store(Request $request)
    {
        $request->validate([
            'files.*'    => 'required|file|max:51200', // 50MB max
            'customer_id'=> 'nullable|integer',
            'project_id' => 'nullable|integer',
            'category'   => 'nullable|string|max:100',
        ]);

        $user = $request->user();
        $saved = [];

        DB::beginTransaction();
        try {
            foreach ($request->file('files', []) as $upload) {
                $orig = $upload->getClientOriginalName();
                $safeBase = Str::slug(pathinfo($orig, PATHINFO_FILENAME));
                $ext = $upload->getClientOriginalExtension();
                $filename = $safeBase.'_'.Str::random(6).'.'.$ext;

                // 📂 Build folder path: year/month/customer/project
                $folder = 'uploads/'.now()->format('Y/m');
                if ($request->filled('customer_id')) {
                    $folder .= '/customer_'.$request->customer_id;
                }
                if ($request->filled('project_id')) {
                    $folder .= '/project_'.$request->project_id;
                }

                // Store in "private" disk
                $path = $upload->storeAs($folder, $filename, 'private');

                $rec = FileDocument::create([
                    'user_id'    => $request->input('customer_id'),
                    'project_id' => $request->input('project_id'),
                    'name'       => $orig,
                    'path'       => $path,
                    'mime'       => $upload->getClientMimeType(),
                    'size'       => $upload->getSize(),
                    'category'   => $request->input('category'),
                    'created_by' => $user ? $user->id : null,
                ]);

                if (! $rec || ! $rec->getKey()) {
                    throw new \RuntimeException('Failed to create file record.');
                }

                $saved[] = [
                    'id'          => $rec->id,
                    'name'        => $rec->name,
                    'mime'        => $rec->mime,
                    'size'        => $rec->size,
                    'path'        => $rec->path,
                    'image_url'   => ($rec->mime && str_starts_with($rec->mime, 'image/')) 
                                        ? route('admin.files.image', ['file' => $rec->id]) 
                                        : null,
                    'download_url'=> route('admin.files.download', ['file' => $rec->id]),
                ];
            }

            DB::commit();
            return response()->json([
                'success'=>true,
                'message'=>count($saved).' file(s) uploaded.',
                'files'=>$saved
            ], 201);

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('File upload failed: '.$e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success'=>false,
                'message'=>'Upload failed. '.$e->getMessage()
            ], 500);
        }
    }


    // GET /admin/files/{file}/image
    public function image(Request $request, FileDocument $file)
    {
        if (! str_starts_with($file->mime ?? '', 'image/')) {
            abort(403, 'Not an image.');
        }

        $disk = Storage::disk('private');
        if (! $disk->exists($file->path)) abort(404);

        return $disk->response($file->path, $file->name, [
            'Content-Type' => $file->mime,
            'Content-Disposition' => 'inline; filename="'.basename($file->name).'"',
            'Cache-Control' => 'private, max-age=300, must-revalidate'
        ]);
    }

    // GET /admin/files/{file}/download
    public function download(Request $request, \App\Models\FileDocument $file)
    {
        // ensure the current user may download (implement policy)
        $this->authorize('download', $file);

        $disk = Storage::disk('private'); // your private disk configured in config/filesystems.php

        if (! $disk->exists($file->path)) {
            abort(404, 'File not found.');
        }

        // Option A: Force download (recommended)
        return $disk->download($file->path, $file->name, [
            'Content-Type' => $file->mime ?? 'application/octet-stream',
            'Cache-Control' => 'private, max-age=60'
        ]);

        // Option B: Stream inline for certain mime types (images/pdf)
        // if (str_starts_with($file->mime ?? '', 'image/') || $file->mime === 'application/pdf') {
        //     return $disk->response($file->path, $file->name, ['Content-Type' => $file->mime]);
        // }
    }

    // DELETE /admin/files/{file}
    public function destroy(FileDocument $file)
    {
        Storage::disk('private')->delete($file->path);
        $file->delete();
        return response()->json(['success'=>true,'message'=>'Deleted']);
    }
}
