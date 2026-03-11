<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateCurrencySeeder extends Seeder
{
    public function run(): void
    {
        // Actualizar folios existentes a COP
        DB::table('folios')->update(['currency' => 'COP']);

        // Actualizar pagos existentes a COP
        DB::table('payments')->update(['currency' => 'COP']);

        echo "\n✅ Moneda actualizada a COP en " . DB::table('folios')->count() . " folios\n";
        echo "✅ Moneda actualizada a COP en " . DB::table('payments')->count() . " pagos\n";
    }
}
