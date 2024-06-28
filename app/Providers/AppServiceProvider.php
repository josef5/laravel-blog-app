<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Policies\PostPolicy;
use App\Models\Post;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Event;

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
        Gate::policy(Post::class, PostPolicy::class);

        Gate::define('visitAdminPages', function ($user) {
            return $user->is_admin === 1;
        });

        Paginator::useBootstrapFive();

        Event::listen(
            'App\Events\OurExampleEvent',
            'App\Listeners\OurExampleListener'
        );
    }
}
