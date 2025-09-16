<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('partials.admin.sidebar', function ($view) {
            $user = auth()->user();

            $menu = collect([
                // Main
                ['section'=>'Main','title'=>'Dashboard','icon'=>'icon-dashboard','route'=>'admin.dashboard','module'=>'dashboard'],
                ['section'=>'Main','title'=>'Customers','icon'=>'icon-customers','route'=>'admin.customers.index','module'=>'customers'],
                ['section'=>'Main','title'=>'Projects','icon'=>'icon-projects','route'=>'admin.projects.index','module'=>'projects'],
                ['section'=>'Main','title'=>'Quotes','icon'=>'icon-quotes','route'=>'admin.quotes.index','module'=>'quotes'],

                // Management
                // ['section'=>'Management','title'=>'Reports','icon'=>'icon-reports','route'=>null,'module'=>'reports'],
                ['section'=>'Management','title'=>'Settings','icon'=>'icon-settings','route'=>null,'module'=>'settings'],
                // ['section'=>'Management','title'=>'Files','icon'=>'icon-files','route'=>'admin.files.index','module'=>'files'],

                // Quick Access
                ['section'=>'Quick Access','title'=>'Starred','icon'=>'icon-starred','route'=>null,'module'=>null],
                ['section'=>'Quick Access','title'=>'Pinned','icon'=>'icon-pinned','route'=>null,'module'=>null],

                // Quotes Management
                ['section'=>'Quotes Management','title'=>'Kitchen Quotes','icon'=>'icon-quotes','route'=>'admin.kitchen-quotes.index','module'=>'kitchen_quotes'],

                // Account (rendered separately too, but kept here for consistency)
                ['section'=>'Account','title'=>'Logout','icon'=>'icon-logout','route'=>'logout','module'=>null,'is_logout'=>true],
            ]);

            // Filter menu by module access
            $filtered = $menu->filter(function ($item) use ($user) {
                // If module is null -> public item (show)
                if (empty($item['module'])) {
                    return true;
                }

                // If no user (guest), hide module-linked items
                if (! $user) return false;

                // User::hasModule handles null-as-admin logic if implemented
                return $user->hasModule($item['module']);
            })->values();

            // Group by section preserving order
            $grouped = $filtered->groupBy('section');
            
            // Pass to view
            $view->with('sidebarMenuSections', $grouped);
        });
    }
}
