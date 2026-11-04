<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Client;
use App\Models\Product;
use App\Models\Provider;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Spatie\Activitylog\Models\Activity;

class AuditController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [new Middleware('permission:ver-auditoria')];
    }

    public function index(Request $request)
    {
        $activities = Activity::query()
            ->with(['causer', 'subject' => function (MorphTo $morphTo) {
                $morphTo->constrain([
                    Category::class => fn ($q) => $q->withTrashed(),
                    Product::class  => fn ($q) => $q->withTrashed(),
                    Client::class   => fn ($q) => $q->withTrashed(),
                    Provider::class => fn ($q) => $q->withTrashed(),
                    Sale::class     => fn ($q) => $q->withTrashed(),
                    Purchase::class => fn ($q) => $q->withTrashed(),
                ]);
            }])
            ->when($request->filled('module'), fn ($q) => $q->where('log_name', $request->input('module')))
            ->when($request->filled('event'), fn ($q) => $q->where('event', $request->input('event')))
            ->when($request->filled('user'), fn ($q) => $q->where('causer_type', (new User)->getMorphClass())->where('causer_id', $request->integer('user')))
            ->when($request->filled('from'), fn ($q) => $q->whereDate('created_at', '>=', $request->input('from')))
            ->when($request->filled('to'), fn ($q) => $q->whereDate('created_at', '<=', $request->input('to')))
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        return view('audit.index', [
            'activities' => $activities,
            'modules'    => Activity::query()->distinct()->orderBy('log_name')->pluck('log_name'),
            'users'      => User::orderBy('name')->get(['id', 'name']),
            'events'     => ['created' => 'Creado', 'updated' => 'Actualizado', 'deleted' => 'Eliminado', 'restored' => 'Restaurado'],
        ]);
    }
}