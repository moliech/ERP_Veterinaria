<nav x-data="{ open: false }" class="bg-white border-b border-gray-200 shadow-sm">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo VetPets -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center space-x-2">
                        <div class="w-10 h-10 bg-emerald-600 rounded-lg flex items-center justify-center shadow-sm">
                            <i class="fa-solid fa-paw text-white text-lg"></i>
                        </div>
                        <span class="text-lg font-bold text-slate-900">VetPets <span class="text-emerald-600">ERP</span></span>
                    </a>
                </div>

                <!-- Enlaces de Módulos del ERP -->
                <div class="hidden space-x-6 sm:-my-px sm:ms-8 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="text-slate-700 font-medium">
                        <i class="fa-solid fa-chart-line mr-2 text-emerald-600"></i> {{ __('Dashboard') }}
                    </x-nav-link>

                    <x-nav-link href="#" class="text-slate-700 font-medium">
                        <i class="fa-solid fa-dog mr-2 text-emerald-600"></i> Pacientes
                    </x-nav-link>

                    <x-nav-link href="#" class="text-slate-700 font-medium">
                        <i class="fa-solid fa-users mr-2 text-emerald-600"></i> Clientes
                    </x-nav-link>

                    <x-nav-link href="#" class="text-slate-700 font-medium">
                        <i class="fa-solid fa-boxes-stacked mr-2 text-emerald-600"></i> Productos
                    </x-nav-link>

                    <x-nav-link href="#" class="text-slate-700 font-medium">
                        <i class="fa-solid fa-cash-register mr-2 text-emerald-600"></i> Ventas
                    </x-nav-link>
                </div>
            </div>

            <!-- Menú de Usuario -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-gray-200 text-sm font-medium rounded-lg text-slate-700 bg-white hover:bg-slate-50 focus:outline-none transition">
                            <div class="w-7 h-7 bg-emerald-100 text-emerald-700 rounded-full flex items-center justify-center mr-2 font-bold text-xs">
                                {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                            </div>
                            <div>{{ Auth::user()->name }}</div>
                            <div class="ms-1">
                                <i class="fa-solid fa-chevron-down text-xs text-gray-400"></i>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            <i class="fa-solid fa-user mr-2 text-gray-400"></i> Perfil de usuario
                        </x-dropdown-link>

                        <!-- Cerrar sesión -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault(); this.closest('form').submit();">
                                <i class="fa-solid fa-right-from-bracket mr-2 text-red-500"></i> Cerrar sesión
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Menú Hamburguesa Móvil -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none transition">
                    <i class="fa-solid" :class="{'fa-xmark': open, 'fa-bars': ! open}"></i>
                </button>
            </div>
        </div>
    </div>
</nav>