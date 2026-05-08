<?php

namespace App\Providers;

use App\Models\Permission as ModelsPermission;
use App\Models\SiteSetting;
use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Spatie\Permission\Models\Permission;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // Super-admin bypass - can do everything
        Gate::before(function (User $user, string $ability) {
            if ($user->isSuperAdmin()) return true;
        });

        // Register all permissions as Gates
        try {
            ModelsPermission::all()->each(function ($permission) {
                Gate::define($permission->name, function (User $user) use ($permission) {
                    return $user->hasPermissionTo($permission->name);
                });
            });
        } catch (\Exception $e) {
            // Database may not be migrated yet
        }

        // Share global view data
        View::composer('admin.*', function ($view) {
            if (auth()->check()) {
                $siteName = SiteSetting::get('site_name', config('app.name'));
                $siteLogo = SiteSetting::get('site_logo');
                $unreadNotifs = auth()->user()->notifications()->whereNull('read_at')->count();
                $view->with(compact('siteName', 'siteLogo', 'unreadNotifs'));
            }
        });
    }
}
