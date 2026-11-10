<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;

    public function login(): void
    {
        $this->validate();
        $this->form->authenticate();
        Session::regenerate();

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<main class="min-h-screen bg-white lg:grid lg:grid-cols-[1.05fr_.95fr]">
    <section class="relative flex min-h-[310px] flex-col justify-between overflow-hidden rounded-b-[38px] bg-[#0E8A9A] px-7 pb-9 pt-7 text-white sm:px-12 lg:min-h-screen lg:rounded-none lg:px-16 lg:py-12">
        <div class="pointer-events-none absolute -right-20 top-24 size-80 rounded-full border border-white/10 sm:size-[430px]"></div>
        <div class="pointer-events-none absolute -right-8 top-44 size-60 rounded-full border border-white/10 sm:size-[340px]"></div>
        <a href="{{ route('login') }}" class="relative z-10 inline-flex w-fit items-center gap-2.5 text-white no-underline" aria-label="ASGEV, inicio">
            <span class="grid size-10 place-items-center rounded-[14px] bg-white/15">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="size-6" aria-hidden="true">
                    <path d="M3 5.5c3.5-.8 6.5 0 9 2.2 2.5-2.2 5.5-3 9-2.2v13c-3.5-.8-6.5 0-9 2.2-2.5-2.2-5.5-3-9-2.2v-13Z"/>
                    <path d="M12 7.7v13"/>
                </svg>
            </span>
            <span class="text-[22px] font-extrabold tracking-[-1.2px]">ASGEV</span>
        </a>
        <div class="relative z-10 mt-12 max-w-lg lg:mt-0">
            <div class="mb-7 grid size-[62px] place-items-center rounded-[20px] bg-white/15">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" class="size-8" aria-hidden="true">
                    <path d="M3 5.5c3.5-.8 6.5 0 9 2.2 2.5-2.2 5.5-3 9-2.2v13c-3.5-.8-6.5 0-9 2.2-2.5-2.2-5.5-3-9-2.2v-13Z"/>
                    <path d="M12 7.7v13"/>
                </svg>
            </div>
            <h1 class="max-w-md text-[34px] font-bold leading-[1.16] tracking-[-1.3px] sm:text-[44px]">Cada paso cuenta en su aprendizaje.</h1>
            <p class="mt-4 text-base font-medium text-white/80">Aprende hoy, lidera mañana.</p>
        </div>
        <p class="relative z-10 mt-10 text-xs font-medium text-white/65 lg:mt-0">Un espacio seguro para crecer juntos.</p>
    </section>

    <section class="flex items-center justify-center px-6 py-12 sm:px-12">
        <div class="w-full max-w-[420px]">
            <p class="text-sm font-bold uppercase tracking-[.13em] text-[#0E8A9A]">Bienvenido a ASGEV</p>
            <h2 class="mt-3 text-[30px] font-extrabold tracking-[-1.1px] text-[#0B4F6C] sm:text-[34px]">Inicia sesión</h2>
            <p class="mt-2 text-sm leading-6 text-[#68808B]">Ingresa con tu correo y contraseña institucional.</p>

            <x-auth-session-status class="mt-5 rounded-xl bg-[#EAF7F4] px-4 py-3 text-sm font-medium text-[#17694F]" :status="session('status')" />

            <form class="mt-8 space-y-5" wire:submit="login">
                <label class="block">
                    <span class="mb-2 block text-sm font-bold text-[#264858]">Correo electrónico</span>
                    <span class="flex h-[54px] items-center gap-3 rounded-2xl border border-[#DFE9EC] bg-white px-4 transition focus-within:border-[#0E8A9A] focus-within:ring-4 focus-within:ring-[#0E8A9A]/10">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="size-[18px] shrink-0 text-[#8AA0A9]" aria-hidden="true">
                            <rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/>
                        </svg>
                        <input class="h-full w-full border-0 bg-transparent text-sm outline-none placeholder:text-[#A2B2B8] focus:ring-0" wire:model="form.email" id="email" name="email" type="email" required autofocus autocomplete="username" placeholder="nombre@colegio.edu.pe">
                    </span>
                    <x-input-error :messages="$errors->get('form.email')" class="mt-2 text-sm text-[#A5362A]" />
                </label>

                <label class="block">
                    <span class="mb-2 block text-sm font-bold text-[#264858]">Contraseña</span>
                    <span class="flex h-[54px] items-center gap-3 rounded-2xl border border-[#DFE9EC] bg-white px-4 transition focus-within:border-[#0E8A9A] focus-within:ring-4 focus-within:ring-[#0E8A9A]/10">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="size-[18px] shrink-0 text-[#8AA0A9]" aria-hidden="true">
                            <rect x="4" y="10" width="16" height="11" rx="2"/><path d="M8 10V7a4 4 0 1 1 8 0v3m-4 5v2"/>
                        </svg>
                        <input class="h-full w-full border-0 bg-transparent text-sm outline-none placeholder:text-[#A2B2B8] focus:ring-0" wire:model="form.password" id="password" name="password" type="password" required autocomplete="current-password" placeholder="Tu contraseña">
                    </span>
                    <x-input-error :messages="$errors->get('form.password')" class="mt-2 text-sm text-[#A5362A]" />
                </label>

                <label class="flex min-h-11 items-center gap-2 text-sm text-[#68808B]">
                    <input wire:model="form.remember" type="checkbox" class="size-4 rounded border-[#C7D7DC] text-[#0E8A9A] focus:ring-[#0E8A9A]">
                    Recordarme
                </label>

                <button class="flex min-h-[54px] w-full items-center justify-center gap-2 rounded-2xl bg-[#0E8A9A] px-5 text-sm font-bold text-white shadow-[0_8px_18px_rgba(14,138,154,.2)] transition hover:bg-[#0B7583] focus:outline-none focus:ring-4 focus:ring-[#0E8A9A]/25 disabled:opacity-60" type="submit" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="login">Ingresar</span>
                    <span wire:loading wire:target="login">Ingresando...</span>
                    <svg wire:loading.remove wire:target="login" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="size-[18px]" aria-hidden="true">
                        <path d="M5 12h14m-6-6 6 6-6 6"/>
                    </svg>
                </button>
            </form>

            @if (Route::has('password.request'))
                <div class="mt-5 text-center">
                    <a class="inline-flex min-h-11 items-center justify-center rounded-lg px-3 text-sm font-semibold text-[#0E8A9A] hover:text-[#0B4F6C] focus:outline-none focus:ring-2 focus:ring-[#0E8A9A]" href="{{ route('password.request') }}" wire:navigate>¿Olvidaste tu contraseña?</a>
                </div>
            @endif
            <p class="mt-6 text-center text-xs leading-5 text-[#8AA0A9]">Tu información está protegida y solo es visible para las personas autorizadas.</p>
        </div>
    </section>
</main>
