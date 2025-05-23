<x-app-layout>
    <div class="min-h-screen flex flex-col items-center justify-center bg-gray-100">
        <div class="w-full max-w-md bg-white shadow-lg rounded-lg overflow-hidden">
            <div class="bg-gray-50 py-4 px-6 border-b border-gray-200">
                <h2 class="text-2xl font-semibold text-gray-800 text-center">Login</h2>
                <p class="text-sm text-gray-600 text-center mt-2">Faça login em sua conta</p>
            </div>

            <div class="p-6">
                <!-- Session Status -->
                <x-auth-session-status class="mb-4" :status="session('status')" />
                
                <!-- Nota de segurança -->                    
                <div class="text-xs text-gray-500 mb-4 p-2 bg-yellow-50 rounded border border-yellow-100">
                    <p class="flex items-center"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4 mr-1 text-yellow-600"><path fill-rule="evenodd" d="M9.401 3.003c1.155-2 4.043-2 5.197 0l7.355 12.748c1.154 2-.29 4.5-2.599 4.5H4.645c-2.309 0-3.752-2.5-2.598-4.5L9.4 3.003zM12 8.25a.75.75 0 01.75.75v3.75a.75.75 0 01-1.5 0V9a.75.75 0 01.75-.75zm0 8.25a.75.75 0 100-1.5.75.75 0 000 1.5z" clip-rule="evenodd" /></svg> Este é o formulário oficial de login da Embaixada Dance Music. Suas informações estão seguras e criptografadas.</p>
                </div>

                <form method="POST" action="{{ route('login') }}" class="space-y-6" data-form-type="login" autocomplete="on">
                    @csrf

                    <!-- Email Address -->
                    <div class="space-y-2">
                        <label for="email" class="text-sm font-medium text-gray-700">Email</label>
                        <div class="relative">
                            <input type="email" name="email" id="email" placeholder="Digite seu email" value="{{ old('email') }}"
                                required autofocus autocomplete="username"
                                class="w-full px-4 py-2 bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block" />
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-gray-400 absolute right-3 top-2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                            </svg>
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="mt-1" />
                    </div>

                    <!-- Password -->
                    <div class="space-y-2">
                        <label for="password" class="text-sm font-medium text-gray-700">Senha</label>
                        <div class="relative">
                            <input type="password" name="password" id="password" placeholder="Digite sua senha" required
                                autocomplete="current-password"
                                class="w-full px-4 py-2 bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block" />
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-gray-400 absolute right-3 top-2.5 cursor-pointer">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-1" />
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}
                                class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500" />
                            <label for="remember" class="ml-2 block text-sm text-gray-700">
                                {{ __('Salvar acesso') }}
                            </label>
                        </div>
                        @if (Route::has('password.request'))
                            <a class="text-sm text-blue-600 hover:underline" href="{{ route('password.request') }}">
                                {{ __('Esqueceu sua senha?') }}
                            </a>
                        @endif
                    </div>

                    <div>
                        <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500" data-sitekey="login-form">
                            {{ __('Entrar') }}
                        </button>
                    </div>
                    
                    <div class="text-xs text-gray-500 mt-2">
                        Ao fazer login, você concorda com nossos <a href="{{ route('terms.service') }}" class="text-blue-600 hover:underline">Termos de Uso</a> e <a href="{{ route('privacy.policy') }}" class="text-blue-600 hover:underline">Política de Privacidade</a>.
                    </div>
                </form>

                <div class="mt-6">
                    <div class="relative">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-300"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-2 bg-white text-gray-500">
                                {{ __('Ou') }}
                            </span>
                        </div>
                    </div>

                    <div class="mt-6 grid grid-cols-3 gap-3">
                        <!-- Add social login buttons here if needed -->
                    </div>
                    <div class="flex justify-center">
                        <a href="{{ route('auth.google') }}" class="w-full flex items-center justify-center py-2 text-sm text-gray-500 border border-gray-300 rounded-md hover:bg-gray-50 hover:text-gray-700 font-medium transform hover:scale-[1.03] transition-all duration-200">
                            <svg class="w-5 h-5 mr-2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12.24 10.285V14.4h6.806c-.275 1.765-2.056 5.174-6.806 5.174-4.095 0-7.439-3.389-7.439-7.574s3.345-7.574 7.439-7.574c2.33 0 3.891.989 4.785 1.849l3.254-3.138C18.189 1.186 15.479 0 12.24 0c-6.635 0-12 5.365-12 12s5.365 12 12 12c6.926 0 11.52-4.869 11.52-11.726 0-.788-.085-1.39-.189-1.989H12.24z" fill="#4285F4"/>
                            </svg>
                            {{ __('Google') }}
                        </a>
                    </div>
                </div>

                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                    <p class="text-center text-sm text-gray-600">
                        {{ __("Você ainda não é cadastrado?") }}
                        <a href="{{ route('register') }}" class="font-medium text-blue-600 hover:text-blue-500">
                            {{ __('Cadastre-se') }}
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

