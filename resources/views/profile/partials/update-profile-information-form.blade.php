<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Informações pessoais:') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("gerencie sua conta, senhas, pagamentos e endereços:") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" :value="__('Nome Completo')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            <div id="email-verification-status" class="mt-2 text-sm">
                @if ($user->hasVerifiedEmail())
                    <p class="text-green-600">{{ __('Seu email está verificado') }}</p>
                @else
                <p class="text-red-600">{{ __('Seu endereço de e-mail não foi verificado.') }}</p>
                <button id="verify-email-button" form="send-verification" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    {{ __('Clique aqui para verificar seu e-mail.') }}
                </button>
                @endif
            </div>

            @if (session('status') === 'verification-link-sent')
                <p class="mt-2 font-medium text-sm text-green-600">
                    {{ __('um link de verificação foi enviado para seu email') }}
                </p>
            @endif
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Salvar') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const verifyEmailButton = document.getElementById('verify-email-button');
            const verifyEmailForm = document.getElementById('send-verification');

            if (verifyEmailButton && verifyEmailForm) {
                verifyEmailForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    verifyEmailButton.textContent = 'Enviando email aguarde...';
                    verifyEmailButton.disabled = true;
                    this.submit();
                });
            }
        });
    </script>
</section>

