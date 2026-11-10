<?php

namespace App\Providers;

use App\Models\Nota;
use App\Observers\NotaObserver;
use Illuminate\Support\ServiceProvider;
use App\Http\Middleware\AplicarColegio;
use Livewire\Livewire;

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
        Nota::observe(NotaObserver::class);
        Livewire::addPersistentMiddleware([AplicarColegio::class]);
    }
}
