<?php

use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    /**
     * Send an email verification notification to the user.
     */
    public function sendVerification(): void
    {
        if (Auth::user()->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);

            return;
        }

        Auth::user()->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }

    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }
}; ?>

<div>
    <div class="mb-4 text-sm leading-6 text-[#68808B]">
        Confirma tu correo electrónico desde el enlace que enviamos a tu bandeja de entrada. Si no lo recibiste, puedes solicitar uno nuevo.
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 rounded-xl bg-[#EAF7F4] px-4 py-3 text-sm font-medium text-[#17694F]">
            Enviamos un nuevo enlace de verificación a tu correo.
        </div>
    @endif

    <div class="mt-4 flex items-center justify-between">
        <x-primary-button wire:click="sendVerification">
            Reenviar correo de verificación
        </x-primary-button>

        <button wire:click="logout" type="submit" class="inline-flex min-h-11 items-center rounded-lg px-3 text-sm font-semibold text-[#68808B] hover:text-[#0B4F6C] focus:outline-none focus:ring-2 focus:ring-[#0E8A9A]">
            Cerrar sesión
        </button>
    </div>
</div>
