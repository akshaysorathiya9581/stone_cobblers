<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\User;
use App\Models\FileDocument;
use App\Models\Quote;
use Carbon\Carbon;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::with('customer')->orderBy('created_at', 'desc')->get();
        $activeProjects = Project::whereNotIn('status', ['Completed', 'Cancelled'])->count();
        $completedProjectsThisMonth  = Project::where('status', 'Completed')->count();
        // $completedProjectsThisMonth = Project::where('status', 'Completed')
        //     ->whereMonth('completed_at', Carbon::now()->month)
        //     ->whereYear('completed_at', Carbon::now()->year)
        //     ->count();

        return view('admin.projects.index', compact('projects', 'activeProjects', 'completedProjectsThisMonth'));

        $user = auth()->user();

        if ($user->isAdmin()) {
            $totalCustomers = User::customers()->count();
            $activeProjects = Project::active()->count();
            $completedProjectsThisMonth = Project::completedThisMonth()->count();

            $customers = User::customers()
                ->withCount('projects')
                ->withSum('projects', 'budget')
                ->get();
        } else {
            $totalCustomers = $user->role === 'customer' ? 1 : 0;
            $activeProjects = $user->projects()->active()->count();
            $completedProjectsThisMonth = $user->projects()->completedThisMonth()->count();

            $user->loadCount('projects')->loadSum('projects', 'budget');
            $customers = collect([$user]);
        }

        return view('admin.projects.index', compact(
            'totalCustomers',
            'activeProjects',
            'completedProjectsThisMonth'
        ));
    }

    public function create()
    {
        $user = auth()->user();

        if ($user->role === 'admin') {
            // Admin: show all customers
            $customers = User::where('role', 'customer')->get();
        } else {
            // Non-admin (customer/other): only themselves
            $customers = collect([$user]);
        }

        return view('admin.projects.create', compact('customers'));
    }

    public function store(Request $request)
    {
        $postData = $request->all();
          // create project (customize fields as your schema)
        $project = Project::create([
            'name'           => $postData['name'],
            'subtitle'       => $postData['subtitle'] ?? null,
            'description'    => $postData['description'] ?? null,
            'user_id'        => $postData['customer_id'], // relation to user/customer
            'customer_notes' => $postData['customer_notes'] ?? null,
            'budget'         => $postData['budget'],
            'timeline'       => $postData['timeline'],
            'status'         => $postData['status'],
            'progress'       => $postData['progress'],
        ]);

        return response()->json([
            'success' => true,
            'project' => $project,
            'message' => 'Project created successfully!',
        ]);
    }

    public function show($id)
    {
        $user = auth()->user();

        // Load project + its customer (or 404)
        $project = Project::with('customer')->findOrFail($id);

        // If not admin, ensure the user actually owns / is the customer for this project
        if ($user->role !== 'admin') {
            // Adjust this check if your project->customer relation uses a different FK
            if (($project->user_id ?? $project->customer->id ?? null) !== $user->id) {
                abort(403, 'Unauthorized access to this project.');
            }

            // Customer: return only their own files & quotes for this project
            $project_files  = FileDocument::where('project_id', $id)->where('user_id', $user->id)->get();
            $project_quotes = Quote::where('project_id', $id)->where('user_id', $user->id)->get();
        } else {
            // Admin: return all files & quotes for this project
            $project_files  = FileDocument::where('project_id', $id)->get();
            $project_quotes = Quote::where('project_id', $id)->get();
        }

        return view('admin.projects.show', [
            'id' => $id,
            'project_details' => $project,
            'project_files' => $project_files,
            'project_quotes' => $project_quotes,
        ]);
    }

    public function edit($id)
    {
        $user = auth()->user();

        if ($user->role === 'admin') {
            // Admin: show all customers
            $customers = User::where('role', 'customer')->get();
        } else {
            // Non-admin (customer/other): only themselves
            $customers = collect([$user]);
        }
        
        $project = Project::where('id', $id)->first();
        return view('admin.projects.edit', compact('id', 'customers', 'project'));
    }

   public function update(Request $request, $id)
    {
        $postData = $request->all();

        // Find the project by ID
        $project = Project::findOrFail($id);

        $project->update([
            'name'           => $postData['name'] ?? $project->name,
            'subtitle'       => $postData['subtitle'] ?? $project->subtitle,
            'description'    => $postData['description'] ?? $project->description,
            'user_id'        => $postData['customer_id'] ?? $project->user_id, // relation to customer
            'customer_notes' => $postData['customer_notes'] ?? $project->customer_notes,
            'budget'         => $postData['budget'] ?? $project->budget,
            'timeline'       => $postData['timeline'] ?? $project->timeline,
            'status'         => $postData['status'] ?? $project->status,
            'progress'       => $postData['progress'] ?? $project->progress,
        ]);

        return response()->json([
            'success' => true,
            'project' => $project->fresh(), // latest data
            'message' => 'Project updated successfully!',
        ]);
    }


    public function destroy($id)
    {
        // delete logic
    }

    /**
     * Return JSON list of projects for a customer.
     */
    public function byCustomer($customerId, Request $request)
    {
        // optional: authorize, validate, etc.
        $projects = Project::where('user_id', $customerId)
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json([
            'ok' => true,
            'data' => $projects
        ]);
    }
}
