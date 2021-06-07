<?php

namespace App\Providers;

use App\Models\Permission;
use App\Models\Post;
use App\Policies\PostPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        Post::class => PostPolicy::class
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        if (Schema::hasTable('permissions')) {

            Gate::before(function($user) {
                if($user->isAdmin()) return true;
            });
            //  define all permissions
            foreach (Permission::all() as $permission) {
                Gate::define($permission->name, function ($user) use ($permission) {
                    return $user->hasPermission($permission);
                });
            }
        }
    }
}
