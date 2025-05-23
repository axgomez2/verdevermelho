<x-app-layout>
    <div class="min-h-screen flex flex-col items-center justify-center bg-gray-100">
        <div class="w-full max-w-md bg-white shadow-lg rounded-lg overflow-hidden">
            <div class="bg-gray-50 py-4 px-6 border-b border-gray-200">
                <h2 class="text-2xl font-semibold text-gray-800 text-center">Cadastro</h2>
                <p class="text-sm text-gray-600 text-center mt-2">Crie sua nova conta</p>
            </div>

            <div class="p-6">
                <form method="POST" action="{{ route('register') }}" class="space-y-6" data-form-type="registration" autocomplete="on">
                    @csrf

                    <!-- Note de segurança -->                    
                    <div class="text-xs text-gray-500 mb-4 p-2 bg-yellow-50 rounded border border-yellow-100">
                        <p class="flex items-center"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4 mr-1 text-yellow-600"><path fill-rule="evenodd" d="M9.401 3.003c1.155-2 4.043-2 5.197 0l7.355 12.748c1.154 2-.29 4.5-2.599 4.5H4.645c-2.309 0-3.752-2.5-2.598-4.5L9.4 3.003zM12 8.25a.75.75 0 01.75.75v3.75a.75.75 0 01-1.5 0V9a.75.75 0 01.75-.75zm0 8.25a.75.75 0 100-1.5.75.75 0 000 1.5z" clip-rule="evenodd" /></svg> Este é o formulário oficial de cadastro da Embaixada Dance Music. Suas informações estão seguras e não serão compartilhadas.</p>
                    </div>
                    <!-- Nome -->
                    <div class="space-y-2">
                        <label for="name" class="text-sm font-medium text-gray-700">Nome</label>
                        <div class="relative">
                            <input type="text" name="name" id="name" placeholder="Digite seu nome completo" value="{{ old('name') }}"
                                required autofocus autocomplete="name"
                                class="w-full px-4 py-2 bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block" />
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-gray-400 absolute right-3 top-2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                            </svg>
                        </div>
                        <x-input-error :messages="$errors->get('name')" class="mt-1" />
                    </div>

                    <!-- Email -->
                    <div class="space-y-2">
                        <label for="email" class="text-sm font-medium text-gray-700">Email</label>
                        <div class="relative">
                            <input type="email" name="email" id="email" placeholder="Digite seu email" value="{{ old('email') }}"
                                required autocomplete="username"
                                class="w-full px-4 py-2 bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block" />
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-gray-400 absolute right-3 top-2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                            </svg>
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="mt-1" />
                    </div>

                    <!-- Senha -->
                    <div class="space-y-2">
                        <label for="password" class="text-sm font-medium text-gray-700">Senha</label>
                        <div class="relative">
                            <input type="password" name="password" id="password" placeholder="Digite sua senha" required
                                autocomplete="new-password"
                                class="w-full px-4 py-2 bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block" />
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-gray-400 absolute right-3 top-2.5 cursor-pointer">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-1" />
                    </div>

                    <!-- Confirmar Senha -->
                    <div class="space-y-2">
                        <label for="password_confirmation" class="text-sm font-medium text-gray-700">Confirmar Senha</label>
                        <div class="relative">
                            <input type="password" name="password_confirmation" id="password_confirmation"
                                placeholder="Confirme sua senha" required autocomplete="new-password"
                                class="w-full px-4 py-2 bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block" />
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-gray-400 absolute right-3 top-2.5 cursor-pointer">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
                    </div>

                    <!-- Verify human -->                    
                    <div class="flex items-center mb-4">
                        <input id="human-verification" name="human_verification" type="checkbox" value="1" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500" required>
                        <label for="human-verification" class="ml-2 text-sm font-medium text-gray-700">Confirmo que sou uma pessoa e não um robô</label>
                    </div>
                   
                    <div>
                        <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500" data-sitekey="registered-form">
                            {{ __('Cadastrar') }}
                        </button>
                    </div>
                    
                    <div class="text-xs text-gray-500 mt-2">
                        Ao se cadastrar, você concorda com nossos <a href="{{ route('terms.service') }}" class="text-blue-600 hover:underline">Termos de Uso</a> e <a href="{{ route('privacy.policy') }}" class="text-blue-600 hover:underline">Política de Privacidade</a>.
                    </div>
                </form>
            </div>

            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                <p class="text-center text-sm text-gray-600">
                    {{ __("Já tem uma conta?") }}
                    <a href="{{ route('login') }}" class="font-medium text-blue-600 hover:text-blue-500">
                        {{ __('Faça login') }}
                    </a>
                </p>
            </div>
        </div>
    </div>
</x-app-layout>

