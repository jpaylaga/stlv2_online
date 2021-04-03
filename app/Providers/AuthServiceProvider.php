<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
        'App\User' => 'App\Policies\UserPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Passport::routes();

        Gate::define('make-requests', function ($user) {
            return $user->is_active;
        });

        // permissions goes here
        Gate::define('manage-users', function($user){
            return in_array($user->role, array('super_admin', 'area_admin', 'coordinator')); 
        });
        
        Gate::define('manage-bets', function ($user) {
            return in_array( $user->role, array( 'usher', 'teller', 'coordinator', 'super_admin' ) );
        });

        Gate::define('edit-area', function ($user) {
            return in_array($user->role, array('area_admin', 'super_admin'));
        });

        Gate::define('view-winners', function ($user) {
            return in_array($user->role, array('area_admin', 'super_admin', 'coordinator'));
        });

        Gate::define('draw-results', function ($user) {
            return in_array($user->role, array('super_admin'));
        });

        Gate::define('view-bet-reports', function ($user) { //highest bet and hot numbers
            return in_array($user->role, array('super_admin', 'area_admin'));
        });

        Gate::define('view-area-reports', function ($user) { 
            return in_array($user->role, array('super_admin'));
        });

        Gate::define('manage-credits', function ($user) {
            return in_array($user->role, array('super_admin'));
        });

        Gate::define('transfer-credits', function ($user) {
            return in_array($user->role, array('super_admin', 'area_admin', 'coordinator'));
        });

        Gate::define('manage-areas', function ($user) {
            return in_array($user->role, array('super_admin'));
        });

        Gate::define('play', function ($user) {
            return in_array($user->role, array('player','teller','usher'));
        });

        // Gate::define('manage-area', function ($user, $area) {
        //     return in_array($user->role, array('area_admin')) && $user->areas->contains($area);
        // });

        
    }
}
