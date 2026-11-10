<?php

use App\Http\Middleware\AplicarColegio;
use App\Models\Colegio;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Role;

it('sends visitors to the sign-in page', function () {
    $this->get('/')
        ->assertRedirect(route('login'));

    $this->get('/dashboard')
        ->assertRedirect(route('login'));
});

it('only queries users from the active tenant after authentication', function () {
    $firstColegio = Colegio::create(['nombre' => 'Colegio Uno']);
    $secondColegio = Colegio::create(['nombre' => 'Colegio Dos']);

    $firstUser = User::factory()->create([
        'colegio_id' => $firstColegio->id,
        'email' => 'first@asgev.test',
    ]);
    User::factory()->create([
        'colegio_id' => $secondColegio->id,
        'email' => 'second@asgev.test',
    ]);

    Route::middleware(['web', 'auth', AplicarColegio::class])
        ->get('/_test/tenant-users', fn () => User::query()->orderBy('id')->pluck('email'));

    $this->actingAs($firstUser)
        ->get('/_test/tenant-users')
        ->assertOk()
        ->assertExactJson(['first@asgev.test']);
});

it('requires a college for every non-superadmin account', function () {
    Role::firstOrCreate(['name' => 'familia', 'guard_name' => 'web']);
    $user = User::factory()->create(['colegio_id' => null]);
    $user->assignRole('familia');

    $this->actingAs($user)
        ->get('/dashboard')
        ->assertForbidden();
});
