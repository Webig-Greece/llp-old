<?php

namespace App\Providers;


// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Passport\Passport;


class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        PatientRecord::class => PatientRecordPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */

    public function boot()
    {
        $this->registerPolicies();
    }
}
