<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\AplicarColegio;
use App\Livewire\Matriculas\CrearMatricula;
use App\Livewire\Notas\CompetenciasEstudiante;
use App\Models\Matricula;

Route::get('/', fn () => auth()->check()
    ? redirect()->route('dashboard')
    : redirect()->route('login'));

Route::middleware(['auth', 'verified', AplicarColegio::class])->group(function () {
    Route::get('dashboard', function () {
        $user = auth()->user();
        $user->loadMissing('colegio');

        $children = $user->hasRole('familia')
            ? $user->hijos()->with(['user', 'matriculas' => fn ($query) => $query
                ->where('estado', 'activa')->with(['seccion.grado.nivel', 'anioEscolar'])])->get()
            : collect();

        return view('dashboard', [
            'user' => $user,
            'children' => $children,
            'unreadNotifications' => $user->unreadNotifications()->count(),
        ]);
    })->name('dashboard');

    Route::get('matriculas/crear', CrearMatricula::class)
        ->middleware('can:create,'.Matricula::class)
        ->name('matriculas.create');

    Route::get('notas', CompetenciasEstudiante::class)
        ->name('notas.index');
});

require __DIR__.'/auth.php';
