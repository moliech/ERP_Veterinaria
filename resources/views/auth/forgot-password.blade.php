<x-guest-layout>
    <!-- Encabezado del Formulario -->
    <div class="mb-6 text-center">
        <h2 class="text-xl font-bold text-gray-900">¿Olvidaste tu contraseña?</h2>
        <p class="text-xs text-gray-600 mt-2 leading-relaxed">
            No hay problema. Ingresa tu correo electrónico y te enviaremos un enlace para restablecer tu contraseña y elegir una nueva.
        </p>
    </div>

    <!-- Estado de la Sesión -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
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
                       placeholder="usuario@vetpets.com" 
                       class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500 text-sm text-gray-900 placeholder-gray-400" />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Botón de Envío -->
        <div class="mt-6">
            <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2.5 px-4 rounded-lg shadow transition duration-150 text-sm uppercase tracking-wider flex items-center justify-center space-x-2">
                <i class="fa-solid fa-paper-plane text-xs"></i>
                <span>Enviar Enlace de Restablecimiento</span>
            </button>
        </div>

        <!-- Volver a Iniciar Sesión -->
        <div class="mt-4 text-center">
            <a href="{{ route('login') }}" class="text-xs text-emerald-600 hover:text-emerald-800 font-semibold transition inline-flex items-center">
                <i class="fa-solid fa-arrow-left mr-1"></i> Volver al inicio de sesión
            </a>
        </div>
    </form>
</x-guest-layout>
