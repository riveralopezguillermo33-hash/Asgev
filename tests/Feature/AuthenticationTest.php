<?php

it('renders the Spanish sign-in page and password recovery link', function () {
    $this->get('/login')
        ->assertOk()
        ->assertSee('Inicia sesión')
        ->assertSee('¿Olvidaste tu contraseña?');
});

it('does not expose public account registration', function () {
    $this->get('/register')->assertNotFound();
});

it('ends the session and returns to sign in', function () {
    $user = \App\Models\User::factory()->create();

    $this->actingAs($user)
        ->post('/logout')
        ->assertRedirect('/');

    $this->assertGuest();
});
