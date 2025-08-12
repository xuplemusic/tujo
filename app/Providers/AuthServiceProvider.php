<?php

namespace App\Providers;

use App\Models\Citizen;
use App\Policies\CitizenPolicy;
use App\Models\Audit;
use App\Models\User;
use App\Policies\AuditPolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Citizen::class => CitizenPolicy::class,
        User::class => UserPolicy::class,
        Audit::class => AuditPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //
    }
}
