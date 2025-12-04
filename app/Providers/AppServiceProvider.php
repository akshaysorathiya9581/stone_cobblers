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
                ['section'=>'Main','title'=>'Dashboard','icon'=>'icon-dashboard','route'=>'admin.dashboard','module'=>'dashboard'],
                ['section'=>'Main','title'=>'Customers','icon'=>'icon-customers','route'=>'admin.customers.index','module'=>'customers'],
                ['section'=>'Main','title'=>'Projects','icon'=>'icon-projects','route'=>'admin.projects.index','module'=>'projects'],

                // Quotes Management (Separate for Kitchen and Vanity)
                ['section'=>'Quotes Management','title'=>'Kitchen Quotes','icon'=>'icon-kitchen-quote','route'=>'admin.kitchen.quotes.index','module'=>'quotes','match_path'=>'kitchen-quotes-management'],
                ['section'=>'Quotes Management','title'=>'Vanity Quotes','icon'=>'icon-vanity-quote','route'=>'admin.vanity.quotes.index','module'=>'quotes','match_path'=>'vanity-quotes-management'],
                ['section'=>'Quotes Management','title'=>'Quote','icon'=>'icon-quote','route'=>'admin.quotes.combined.index','module'=>'quotes','match_path'=>'quotes-combined'],

                // Price Management (Configuration)
                ['section'=>'Line Items','title'=>'Kitchen Rates','icon'=>'icon-price-tag','route'=>'admin.kitchen-quotes.index','module'=>'kitchen_quotes','match_path'=>'kitchen-quotes'],
                ['section'=>'Line Items','title'=>'Vanity Rates','icon'=>'icon-calculator','route'=>'admin.vanity-quotes.index','module'=>'kitchen_quotes','match_path'=>'vanity-price-quotes'],

                // Management
                // ['section'=>'Management','title'=>'Reports','icon'=>'icon-reports','route'=>null,'module'=>'reports'],
                ['section'=>'Management','title'=>'Settings','icon'=>'icon-settings','route'=>'admin.settings.index','module'=>'settings'],
                ['section'=>'Management','title'=>'Files','icon'=>'icon-files','route'=>'admin.files.index','module'=>'files'],

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
