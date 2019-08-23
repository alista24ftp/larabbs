<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
		 \App\Models\Topic::class => \App\Policies\TopicPolicy::class,
        // 'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // edit policy name finding
        Gate::guessPolicyNamesUsing(function($modelClass){
            // dynamically return model's corresponding policy name
            // eg. 'App\Model\User' => 'App\Policies\UserPolicy'
            return 'App\Policies\\'.class_basename($modelClass).'Policy';
        });
    }
}
