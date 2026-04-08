<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BackupSetting extends Model
{
    use HasFactory;

    public const FREQUENCIES = ['daily', 'weekly', 'monthly'];

    protected $fillable = [
        'frequency',
        'last_run_at',
        'last_status',
        'last_message',
    ];

    protected $casts = [
        'last_run_at' => 'datetime',
    ];

    public static function current(): self
    {
        $existing = static::query()->first();

        if ($existing instanceof self) {
            return $existing;
        }

        return static::query()->create([
            'frequency' => config('backup.default_frequency', 'daily'),
            'last_status' => null,
            'last_message' => 'Aún no se ha generado ningún backup desde este panel.',
        ]);
    }
}
