<x-guest-layout>
    <!-- Estado de la Sesión -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <!-- Encabezado del Formulario -->
    <div class="mb-6 text-center">
        <h2 class="text-xl font-bold text-gray-900">Iniciar sesión</h2>
        <p class="text-xs text-gray-500 mt-1">Ingresa tus credenciales para acceder al sistema de gestión veterinaria</p>
    </div>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Correo Electrónico -->
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700">Correo electrónico</label>
            <div class="mt-1 relative rounded-md shadow-sm">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                    <i class="fa-solid fa-envelope text-sm"></i>
                </div>
                <input id="email" 
                       type="email" 
                       name="email" 
                       value="{{ old('email') }}" 
                       required 
                       autofocus 
                       autocomplete="username" 
                       placeholder="usuario@vetpets.com" 
                       class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500 text-sm text-gray-900 placeholder-gray-400" />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Contraseña -->
        <div class="mt-4">
            <div class="flex items-center justify-between">
                <label for="password" class="block text-sm font-medium text-gray-700">Contraseña</label>
                @if (Route::has('password.request'))
                    <a class="text-xs text-emerald-600 hover:text-emerald-800 font-medium" href="{{ route('password.request') }}">
                        ¿Olvidaste tu contraseña?
                    </a>
                @endif
            </div>
            <div class="mt-1 relative rounded-md shadow-sm">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                    <i class="fa-solid fa-lock text-sm"></i>
                </div>
                <input id="password" 
                       type="password" 
                       name="password" 
                       required 
                       autocomplete="current-password" 
                       placeholder="••••••••" 
                       class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500 text-sm text-gray-900 placeholder-gray-400" />
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Recordarme -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" 
                       type="checkbox" 
                       name="remember" 
                       class="rounded border-gray-300 text-emerald-600 shadow-sm focus:ring-emerald-500" />
                <span class="ms-2 text-xs text-gray-600">Recordar mi sesión en este equipo</span>
            </label>
        </div>

        <!-- Botón de Inicio de Sesión -->
        <div class="mt-6">
            <button type="submit" 
                    class="w-full flex justify-center items-center py-2.5 px-4 border border-transparent rounded-lg shadow-sm text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition duration-150">
                <i class="fa-solid fa-right-to-bracket mr-2 text-sm"></i>
                Iniciar sesión
            </button>
        </div>

        <!-- Enlace a Registro -->
        <div class="mt-6 text-center text-xs text-gray-600 border-t border-gray-100 pt-4">
            ¿Aún no tienes cuenta de usuario? 
            <a href="{{ route('register') }}" class="font-semibold text-emerald-600 hover:text-emerald-800">
                Registrarse aquí
            </a>
        </div>
    </form>
</x-guest-layout>