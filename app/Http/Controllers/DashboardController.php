<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Project;
use App\Services\DashboardService;

class DashboardController extends Controller
{
    public function index()
    {
        $data = DashboardService::forUser(auth()->user());
        
        return view('admin.dashboard.index', $data);
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
