<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Auditoría Forense — Bitácora de Cambios</h2>
    </x-slot>

    @php
        $eventColors = [
            'created'  => 'bg-emerald-100 text-emerald-800 border-emerald-200',
            'updated'  => 'bg-blue-100 text-blue-800 border-blue-200',
            'deleted'  => 'bg-rose-100 text-rose-800 border-rose-200',
            'restored' => 'bg-amber-100 text-amber-800 border-amber-200',
        ];
        $show = fn ($v) => is_array($v) ? (implode(', ', $v) ?: '(vacío)') : (is_bool($v) ? ($v ? 'sí' : 'no') : ($v ?? '—'));
    @endphp

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">
            <div class="bg-white shadow rounded p-6 space-y-4">
                <form method="GET" action="{{ route('audit.index') }}" class="flex flex-wrap items-end gap-2">
                    <div>
                        <x-input-label for="module" value="Módulo" />
                        <select id="module" name="module" class="mt-1 border-gray-300 rounded-md text-sm">
                            <option value="">Todos</option>
                            @foreach ($modules as $module)
                                <option value="{{ $module }}" @selected(request('module') === $module)>{{ ucfirst($module) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label for="event" value="Evento" />
                        <select id="event" name="event" class="mt-1 border-gray-300 rounded-md text-sm">
                            <option value="">Todos</option>
                            @foreach ($events as $value => $label)
                                <option value="{{ $value }}" @selected(request('event') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label for="user" value="Usuario" />
                        <select id="user" name="user" class="mt-1 border-gray-300 rounded-md text-sm">
                            <option value="">Todos</option>
                            @foreach ($users as $u)
                                <option value="{{ $u->id }}" @selected((int) request('user') === $u->id)>{{ $u->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label for="from" value="Desde" />
                        <input id="from" type="date" name="from" value="{{ request('from') }}" class="mt-1 border-gray-300 rounded-md text-sm">
                    </div>
                    <div>
                        <x-input-label for="to" value="Hasta" />
                        <input id="to" type="date" name="to" value="{{ request('to') }}" class="mt-1 border-gray-300 rounded-md text-sm">
                    </div>
                    <x-primary-button>Filtrar</x-primary-button>
                </form>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="border-b text-left bg-slate-50">
                                <th class="p-2.5">Fecha / Hora</th>
                                <th class="p-2.5">Usuario</th>
                                <th class="p-2.5">Módulo</th>
                                <th class="p-2.5">Evento</th>
                                <th class="p-2.5">Registro</th>
                                <th class="p-2.5">Cambios Detectados (Visual Diffs)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($activities as $activity)
                                @php
                                    $changes = $activity->attribute_changes ?? $activity->properties;
                                    $new = is_array($changes) ? ($changes['attributes'] ?? []) : ($changes?->get('attributes', []) ?? []);
                                    $old = is_array($changes) ? ($changes['old'] ?? []) : ($changes?->get('old', []) ?? []);
                                @endphp
                                <tr class="border-b align-top hover:bg-slate-50/70 transition">
                                    <td class="p-2.5 whitespace-nowrap font-mono text-xs text-slate-600">{{ $activity->created_at->format('d/m/Y H:i:s') }}</td>
                                    <td class="p-2.5 font-medium">{{ $activity->causer?->name ?? 'Sistema' }}</td>
                                    <td class="p-2.5"><span class="px-2 py-0.5 rounded text-xs font-mono bg-slate-100 text-slate-700">{{ $activity->log_name }}</span></td>
                                    <td class="p-2.5">
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold border {{ $eventColors[$activity->event] ?? 'bg-gray-100' }}">
                                            {{ $activity->description }}
                                        </span>
                                    </td>
                                    <td class="p-2.5">
                                        <span class="font-medium text-slate-800">{{ class_basename($activity->subject_type) }} #{{ $activity->subject_id }}</span>
                                        <span class="block text-xs text-slate-500">{{ $activity->subject?->name ?? $activity->subject?->invoice_number ?? $activity->subject?->purchase_number }}</span>
                                    </td>
                                    <td class="p-2.5">
                                        <ul class="space-y-1.5">
                                            @foreach ($new as $field => $value)
                                                <li class="text-xs flex items-center flex-wrap gap-1.5">
                                                    <span class="font-mono font-bold text-slate-700">{{ $field }}:</span>
                                                    @if (array_key_exists($field, $old))
                                                        <span class="px-2 py-0.5 rounded bg-rose-50 text-rose-700 border border-rose-200 line-through">{{ $show($old[$field]) }}</span>
                                                        <i class="fa-solid fa-arrow-right text-[10px] text-slate-400"></i>
                                                    @endif
                                                    <span class="px-2 py-0.5 rounded bg-emerald-50 text-emerald-800 border border-emerald-200 font-semibold">{{ $show($value) }}</span>
                                                </li>
                                            @endforeach
                                            @if (empty($new) && ! empty($old))
                                                @foreach ($old as $field => $value)
                                                    <li class="text-xs flex items-center gap-1.5">
                                                        <span class="font-mono font-bold text-slate-700">{{ $field }}:</span>
                                                        <span class="px-2 py-0.5 rounded bg-rose-50 text-rose-700 border border-rose-200 line-through">{{ $show($value) }}</span>
                                                    </li>
                                                @endforeach
                                            @endif
                                        </ul>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="p-6 text-center text-gray-500">No hay actividad registrada en la bitácora.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                {{ $activities->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
