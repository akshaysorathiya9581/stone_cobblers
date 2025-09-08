<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckModuleAccess
{
    /**
     * Usage in routes: ->middleware('module:projects')
     */
    public function handle(Request $request, Closure $next, string $module)
    {
        // echo $module; die();
        $user = $request->user();

        if (! $user) {
            // not logged in
            return redirect()->route('login');
        }

        // Decide admin behavior:
        // If you want null modules to mean "all access" (admin), you can check:
        // if ($user->modules === null) { return $next($request); }

        if (! $user->hasModule($module)) {
            abort(403, 'Unauthorized - module access denied');
        }

        return $next($request);
    }
}

