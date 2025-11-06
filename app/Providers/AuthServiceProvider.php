<?php 
use App\Policies\GeneralPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
}

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::define('delete-item', [GeneralPolicy::class, 'delete']);
        Gate::define('update-item', [GeneralPolicy::class, 'update']);
        Gate::define('create-item', [GeneralPolicy::class, 'create']);
        Gate::define('view-suppliers', [GeneralPolicy::class, 'viewSuppliers']);
    }
}