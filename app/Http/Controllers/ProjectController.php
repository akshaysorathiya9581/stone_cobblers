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
        return view('admin.projects.edit', compact('id'));
    }

    public function update(Request $request, $id)
    {
        // update logic
    }

    public function destroy($id)
    {
        // delete logic
    }
}
