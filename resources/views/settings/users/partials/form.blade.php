@if ($errors->any())
    <div class="p-3 rounded bg-red-100 text-red-800">
        <ul class="list-disc list-inside">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<h3 class="font-semibold text-gray-800">1. Datos del usuario</h3>
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
        <x-input-label for="name" value="Nombre" />
        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required />
    </div>
    <div>
        <x-input-label for="email" value="Correo" />
        <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required />
    </div>
    <div>
        <x-input-label for="password" :value="$user->exists ? 'Nueva contraseña (dejar vacío para no cambiarla)' : 'Contraseña'" />
        <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" autocomplete="new-password" />
    </div>
    <div>
        <x-input-label for="password_confirmation" value="Confirmar contraseña" />
        <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" autocomplete="new-password" />
    </div>
</div>
<label class="inline-flex items-center gap-2">
    <input type="checkbox" name="active" value="1" class="rounded border-gray-300" @checked(old('active', $user->active))>
    <span>Usuario activo (puede iniciar sesión)</span>
</label>

<h3 class="font-semibold text-gray-800 pt-4 border-t">2. Roles y Clonación de Privilegios</h3>

@if (! empty($templateUsers))
    <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <span class="block text-xs font-bold text-slate-800">Copiar roles y permisos de un usuario existente</span>
            <span class="block text-xs text-slate-500">Seleccione un usuario de referencia para replicar su perfil en un clic.</span>
        </div>
        <div class="flex items-center gap-2">
            <select id="clone-user-select" class="border-slate-300 rounded-lg text-xs py-2 px-3">
                <option value="">— Seleccionar usuario modelo —</option>
                @foreach ($templateUsers as $tpl)
                    <option value="{{ $tpl['id'] }}">{{ $tpl['name'] }} ({{ $tpl['email'] }})</option>
                @endforeach
            </select>
            <button type="button" id="clone-user-btn"
                    class="px-3.5 py-2 rounded-lg bg-slate-800 hover:bg-slate-900 text-white text-xs font-semibold transition whitespace-nowrap">
                <i class="fa-solid fa-copy mr-1"></i> Aplicar perfil
            </button>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const templates = @json($templateUsers);
            const select = document.getElementById('clone-user-select');
            const btn = document.getElementById('clone-user-btn');
            if (!btn || !select) return;

            btn.addEventListener('click', () => {
                const tpl = templates.find(u => String(u.id) === String(select.value));
                if (!tpl) return;

                document.querySelectorAll('input[name="roles[]"]').forEach(cb => {
                    cb.checked = tpl.roles.includes(cb.value);
                });
                document.querySelectorAll('input[name="permissions[]"]').forEach(cb => {
                    cb.checked = tpl.permissions.includes(cb.value);
                });
            });
        });
    </script>
@endif

<p class="text-sm text-gray-600">
    Puede asignar varios roles. Si el usuario tiene más de uno, al iniciar sesión elegirá con cuál trabajar.
</p>
<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-2">
    @foreach ($roles as $role)
        <label class="flex items-start gap-2 rounded border p-2">
            <input type="checkbox" name="roles[]" value="{{ $role->name }}" class="mt-1 rounded border-gray-300"
                   @checked(in_array($role->name, $userRoles))>
            <span>
                <span class="block font-medium">{{ $role->name }}</span>
                <span class="block text-xs text-gray-500">{{ $role->permissions->count() }} permisos</span>
            </span>
        </label>
    @endforeach
</div>

<h3 class="font-semibold text-gray-800 pt-4 border-t">3. Permisos directos (adicionales a los roles)</h3>
<p class="text-sm text-gray-600">
    Úselos para excepciones puntuales (ej.: un vendedor que también puede ver reportes).
    Los permisos directos aplican con <strong>cualquier</strong> rol activo. La etiqueta «por rol» indica que
    el permiso ya lo recibe por uno de sus roles (no es necesario marcarlo).
</p>
<x-permission-grid :groups="$groups" :actions="$actions" :selected="$directPermissions" :inherited="$inherited" />
