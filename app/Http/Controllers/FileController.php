<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\FileModel;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;

class FileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
       $user = auth()->user();

        if ($user && $user->role === 'admin') {
            $customers = User::where('role', 'customer')->get();
            $projects  = Project::all();
        } else {
            // non-admin: only themselves and their projects
            $customers = $user ? collect([$user]) : collect();
            $projects  = $user ? Project::where('user_id', $user->id)->get() : collect();
        }

        return view('admin.file.index', compact('customers', 'projects'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(FileModel $fileModel)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(FileModel $fileModel)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, FileModel $fileModel)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FileModel $fileModel)
    {
        //
    }
}
