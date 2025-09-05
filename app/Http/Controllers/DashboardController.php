<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Project;

class DashboardController extends Controller
{
    public function index()
    {
        $totalCustomers   = User::count();
        $activeProjects = Project::whereNotIn('status', ['Completed', 'Cancelled'])->count();

        return view('admin.dashboard.index', compact('totalCustomers', 'activeProjects'));
    }

    public function create()
    {
        return view('admin.dashboard.create');
    }

    public function store(Request $request)
    {
        // store logic
    }

    public function show($id)
    {
        return view('admin.dashboard.show', compact('id'));
    }

    public function edit($id)
    {
        return view('admin.dashboard.edit', compact('id'));
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
