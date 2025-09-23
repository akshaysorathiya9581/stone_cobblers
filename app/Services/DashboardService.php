<?php
// app/Services/DashboardService.php
namespace App\Services;

use App\Models\User;
use App\Models\Project;
use App\Models\Quote;
use Illuminate\Support\Collection;

class DashboardService
{
    public static function forUser(User $user): array
    {
        // Helper to build pending quote array
        $buildPending = function (int $draftCount, int $sentCount) : array {
            return [
                'draft' => $draftCount,
                'sent'  => $sentCount,
                'total' => $draftCount + $sentCount,
            ];
        };

        if ($user->isAdmin()) {
            return [
                'totalCustomers' => User::customers()->count(),
                'activeProjects' => Project::active()->count(),
                'customers' => User::customers()
                    ->withCount('projects')
                    ->withSum('projects', 'budget')
                    ->get(),

                // Pending quotes (global) for admin
                'pendingQuotes' => $buildPending(
                    Quote::where('status', 'Draft')->count(),
                    Quote::where('status', 'Sent')->count()
                ),
            ];
        }

        // Non-admin: preload project counts/sums on the user
        $user->loadCount('projects')->loadSum('projects','budget');

        // If the user is a customer, calculate their own draft/sent quotes
        if ($user->role === 'customer') {
            $draftCount = $user->quotes()->where('status', 'Draft')->count();
            $sentCount  = $user->quotes()->where('status', 'Sent')->count();

            return [
                'totalCustomers' => 1,
                'activeProjects' => $user->projects()->active()->count(),
                // return a collection with the single user so callers expecting a collection still work
                'customers' => collect([$user]),
                'pendingQuotes' => $buildPending($draftCount, $sentCount),
            ];
        }

        // Fallback for other roles (e.g., manager) — customize as needed
        return [
            'totalCustomers' => 0,
            'activeProjects' => $user->projects()->active()->count(),
            'customers' => collect([]),
            'pendingQuotes' => $buildPending(0, 0),
        ];
    }
}
