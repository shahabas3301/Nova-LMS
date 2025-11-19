<?php

namespace Modules\Forumwise\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\Forumwise\Models\Forum;
use Modules\Forumwise\Models\Topic;
use Modules\Forumwise\Models\Category;
use Modules\Forumwise\Observers\ForumObserver;
use Modules\Forumwise\Observers\TopicObserver;
use Modules\Forumwise\Observers\CategoryObserver;
    

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event handler mappings for the application.
     *
     * @var array<string, array<int, string>>
     */
    protected $listen = [];

    protected $observers = [
        Forum::class              => [ForumObserver::class],
        Topic::class                 => [TopicObserver::class],
        Category::class         => [CategoryObserver::class],
    ];
    /**
     * Indicates if events should be discovered.
     *
     * @var bool
     */
    protected static $shouldDiscoverEvents = true;

    /**
     * Configure the proper event listeners for email verification.
     */
    protected function configureEmailVerification(): void
    {
        //
    }
}
