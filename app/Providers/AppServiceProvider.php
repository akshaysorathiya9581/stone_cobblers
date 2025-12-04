<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;

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
        
        Schema::defaultStringLength(191);

        View::composer('partials.admin.sidebar', function ($view) {
            $user = auth()->user();

            $menu = collect([
                // Main
                ['section'=>'Main','title'=>'Dashboard','icon'=>'fas fa-home','route'=>'admin.dashboard','module'=>'dashboard'],
                ['section'=>'Main','title'=>'Customers','icon'=>'fas fa-users','route'=>'admin.customers.index','module'=>'customers'],
                ['section'=>'Main','title'=>'Projects','icon'=>'fas fa-folder-open','route'=>'admin.projects.index','module'=>'projects'],

                // Quotes Management (Separate for Kitchen and Vanity)
                ['section'=>'Quotes Management','title'=>'Kitchen Quotes','icon'=>'fas fa-utensils','route'=>'admin.kitchen.quotes.index','module'=>'quotes','match_path'=>'kitchen-quotes-management'],
                ['section'=>'Quotes Management','title'=>'Vanity Quotes','icon'=>'fas fa-bath','route'=>'admin.vanity.quotes.index','module'=>'quotes','match_path'=>'vanity-quotes-management'],
                ['section'=>'Quotes Management','title'=>'Quote','icon'=>'fas fa-file-invoice-dollar','route'=>'admin.quotes.combined.index','module'=>'quotes','match_path'=>'quotes-combined'],

                // Price Management (Configuration)
                ['section'=>'Line Items','title'=>'Kitchen Rates','icon'=>'fas fa-tags','route'=>'admin.kitchen-quotes.index','module'=>'kitchen_quotes','match_path'=>'kitchen-quotes'],
                ['section'=>'Line Items','title'=>'Vanity Rates','icon'=>'fas fa-calculator','route'=>'admin.vanity-quotes.index','module'=>'kitchen_quotes','match_path'=>'vanity-price-quotes'],

                // Management
                // ['section'=>'Management','title'=>'Reports','icon'=>'fas fa-chart-bar','route'=>null,'module'=>'reports'],
                ['section'=>'Management','title'=>'Settings','icon'=>'fas fa-cog','route'=>'admin.settings.index','module'=>'settings'],
                ['section'=>'Management','title'=>'Files','icon'=>'fas fa-folder','route'=>'admin.files.index','module'=>'files'],

                // Account (rendered separately too, but kept here for consistency)
                ['section'=>'Account','title'=>'Logout','icon'=>'fas fa-sign-out-alt','route'=>'logout','module'=>null,'is_logout'=>true],
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
