<?php

namespace App\Features\Community;

use App\Features\Community\Models\Channel;
use App\Features\Community\Models\Comment;
use App\Features\Community\Models\Post;
use App\Features\Community\Policies\ChannelPolicy;
use App\Features\Community\Policies\CommentPolicy;
use App\Features\Community\Policies\PostPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class CommunityFeatureProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/Migrations');

        Route::prefix('api')
            ->middleware('api')
            ->group(__DIR__ . '/Routes/api.php');

        $this->registerPolicies();
    }

    /**
     * Register policies.
     */
    protected function registerPolicies(): void
    {
        Gate::policy(Channel::class, ChannelPolicy::class);
        Gate::policy(Post::class, PostPolicy::class);
        Gate::policy(Comment::class, CommentPolicy::class);
    }
}
