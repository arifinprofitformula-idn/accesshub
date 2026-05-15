<?php

namespace App\Providers;

use App\Listeners\LogSuccessfulLogin;
use App\Listeners\LogSuccessfulLogout;
use App\Models\AccessItem;
use App\Models\Category;
use App\Models\Link;
use App\Models\User;
use App\Policies\AccessItemPolicy;
use App\Policies\CategoryPolicy;
use App\Policies\LinkPolicy;
use App\Policies\UserPolicy;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Throwable;

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
        if (in_array(DB::getDriverName(), ['mysql', 'mariadb'], true)) {
            Schema::defaultStringLength(191);
        }

        $this->useFileBackedStoresUntilDatabaseTablesExist();

        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(Category::class, CategoryPolicy::class);
        Gate::policy(Link::class, LinkPolicy::class);
        Gate::policy(AccessItem::class, AccessItemPolicy::class);

        Event::listen(Login::class, LogSuccessfulLogin::class);
        Event::listen(Logout::class, LogSuccessfulLogout::class);
    }

    /**
     * Prevent Artisan commands from failing on fresh installs before
     * database-backed cache/session tables have been migrated.
     */
    protected function useFileBackedStoresUntilDatabaseTablesExist(): void
    {
        if (! $this->app->runningInConsole()) {
            return;
        }

        try {
            if (config('cache.default') === 'database' && ! Schema::hasTable(config('cache.stores.database.table', 'cache'))) {
                config(['cache.default' => 'file']);
            }

            if (config('session.driver') === 'database' && ! Schema::hasTable(config('session.table', 'sessions'))) {
                config(['session.driver' => 'file']);
            }
        } catch (Throwable) {
            config([
                'cache.default' => 'file',
                'session.driver' => 'file',
            ]);
        }
    }
}
