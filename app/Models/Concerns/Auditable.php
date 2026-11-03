<?php

namespace App\Models\Concerns;

use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

/**
 * Trait Auditable
 *
 * Estandariza la configuración de auditoría forense mediante spatie/laravel-activitylog (v5).
 * - Registra cambios en attributes y old dentro de la columna JSON attribute_changes.
 * - Omite eventos vacíos donde no haya mutación real de datos.
 * - Registra únicamente atributos modificados (dirty).
 */
trait Auditable
{
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontLogEmptyChanges()
            ->useLogName(strtolower(class_basename($this)))
            ->setDescriptionForEvent(fn (string $eventName) => sprintf(
                '%s %s #%s',
                $this->auditActionDescription($eventName),
                class_basename($this),
                $this->getKey() ?? 'nuevo'
            ));
    }

    /**
     * Mapeo de nombres legibles para atributos auditados.
     * Sobrescribir en el modelo cuando se requiera personalizar.
     *
     * @return array<string, string>
     */
    protected function auditAttributeNames(): array
    {
        return [];
    }

    /**
     * Descripción textual en español para cada evento del ciclo de vida.
     */
    protected function auditActionDescription(string $eventName): string
    {
        return match ($eventName) {
            'created'  => 'Creación de',
            'updated'  => 'Actualización de',
            'deleted'  => 'Eliminación de',
            'restored' => 'Restauración de',
            default    => ucfirst($eventName) . ' en',
        };
    }
}
