<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-2xl text-slate-800 leading-tight flex items-center gap-2">
                    <i class="fa-solid fa-bell text-teal-600"></i> Centro de Notificaciones
                </h2>
                <p class="text-sm text-slate-500 mt-1">Historial de alertas del sistema, avisos de inventario y recepciones de mercancía.</p>
            </div>
            @if (auth()->user()->unreadNotifications()->exists())
                <form method="POST" action="{{ route('notifications.read-all') }}">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-white border border-slate-300 hover:bg-slate-50 rounded-xl text-xs font-bold text-slate-700 transition flex items-center gap-1.5 shadow-xs">
                        <i class="fa-solid fa-check-double text-teal-600"></i> Marcar todas como leídas
                    </button>
                </form>
            @endif
        </div>
    </x-slot>

    <div class="py-6 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">
        @if (session('success'))
            <div class="bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-xl text-xs text-emerald-800 font-bold shadow-xs">
                <i class="fa-solid fa-circle-check mr-1.5"></i> {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs divide-y divide-slate-100 overflow-hidden">
            @forelse ($notifications as $notification)
                <div class="p-4 flex items-start justify-between gap-4 {{ $notification->read_at ? 'bg-white opacity-75' : 'bg-teal-50/40' }} hover:bg-slate-50 transition">
                    <div class="flex items-start space-x-3 min-w-0">
                        <span class="w-8 h-8 rounded-lg flex items-center justify-center text-xs mt-0.5 {{ $notification->read_at ? 'bg-slate-100 text-slate-500' : 'bg-teal-600 text-white shadow-sm' }}">
                            <i class="fa-solid {{ str_contains($notification->data['title'] ?? '', 'Stock') ? 'fa-triangle-exclamation' : 'fa-box' }}"></i>
                        </span>
                        <div>
                            <div class="flex items-center gap-2">
                                <h4 class="text-sm font-bold text-slate-800">{{ $notification->data['title'] ?? 'Notificación' }}</h4>
                                @if (!$notification->read_at)
                                    <span class="w-2 h-2 rounded-full bg-teal-500"></span>
                                @endif
                            </div>
                            <p class="text-xs text-slate-600 mt-0.5">{{ $notification->data['message'] ?? '' }}</p>
                            <span class="text-[10px] text-slate-400 font-mono mt-1 block">{{ $notification->created_at->diffForHumans() }}</span>
                        </div>
                    </div>

                    @if (!$notification->read_at)
                        <form method="POST" action="{{ route('notifications.read', $notification->id) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="text-xs font-bold text-teal-600 hover:text-teal-800 bg-white border border-teal-200 hover:border-teal-300 px-3 py-1.5 rounded-lg transition whitespace-nowrap">
                                Ver y leer <i class="fa-solid fa-arrow-right ml-1"></i>
                            </button>
                        </form>
                    @endif
                </div>
            @empty
                <div class="p-8 text-center text-slate-400 space-y-2">
                    <i class="fa-regular fa-bell-slash text-3xl"></i>
                    <p class="text-sm font-medium">No tiene notificaciones registradas en su bandeja.</p>
                </div>
            @endforelse
        </div>

        <div class="pt-2">
            {{ $notifications->links() }}
        </div>
    </div>
</x-app-layout>