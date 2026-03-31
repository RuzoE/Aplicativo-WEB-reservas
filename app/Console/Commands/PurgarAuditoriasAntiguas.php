<?php

namespace App\Console\Commands;

use App\Models\Auditoria;
use Illuminate\Console\Command;

class PurgarAuditoriasAntiguas extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auditoria:purge {--days= : Dias de retencion de auditorias}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Elimina auditorias antiguas para evitar saturacion de base de datos';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $days = (int) ($this->option('days') ?: config('auditoria.cleanup.retention_days', 90));

        if ($days <= 0) {
            $this->error('El numero de dias debe ser mayor a cero.');

            return self::INVALID;
        }

        $deleted = Auditoria::where('created_at', '<', now()->subDays($days))->delete();

        $this->info("Auditorias eliminadas: {$deleted}. Retencion aplicada: {$days} dias.");

        return self::SUCCESS;
    }
}
