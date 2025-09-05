<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\User;
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
    }

    public function create()
    {
        // Get all users with customer role
        $customers = User::where('role', 'customer')->get();

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
            'team'           => $postData['team'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'project' => $project,
            'message' => 'Project created successfully!',
        ]);
    }

    public function show($id)
    {
        return view('admin.projects.show', compact('id'));
    }

    public function edit($id)
    {
        $customers = User::where('role', 'customer')->get();
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
            'team'           => $postData['team'] ?? $project->team,
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
}
