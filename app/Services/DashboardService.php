<?php
// app/Services/DashboardService.php
namespace App\Services;

use App\Models\User;
use App\Models\Project;

class DashboardService
{
    public static function forUser(User $user): array
    {
        if ($user->isAdmin()) {
            return [
                'totalCustomers' => User::customers()->count(),
                'activeProjects' => Project::active()->count(),
                'customers' => User::customers()
                    ->withCount('projects')
                    ->withSum('projects', 'budget')
                    ->get(),
            ];
        }

        $user->loadCount('projects')->loadSum('projects','budget');

        return [
            'totalCustomers' => $user->role === 'customer' ? 1 : 0,
            'activeProjects' => $user->projects()->active()->count(),
            'customers' => collect([$user]),
        ];
    }
}
