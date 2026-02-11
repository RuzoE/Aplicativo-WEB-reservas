<?php

namespace App\Jobs\Reception;

use App\Models\Stay;
use App\Models\Charge;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class NightAuditPostRoomRates implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        // Get all in-house stays
        $stays = Stay::with(['room', 'folios'])
            ->where('status', 'InHouse')
            ->get();

        foreach ($stays as $stay) {
            $openFolio = $stay->folios()->where('status', 'Open')->first();

            if (!$openFolio) {
                continue;
            }

            // Check if room rate already posted today
            $alreadyPosted = $openFolio->charges()
                ->where('source', 'RoomRate')
                ->whereDate('posted_at', now()->toDateString())
                ->exists();

            if ($alreadyPosted) {
                continue;
            }

            // Post room rate charge
            $charge = new Charge([
                'source' => 'RoomRate',
                'description' => 'Room Rate - ' . ($stay->room->name ?? 'Room'),
                'amount' => $stay->daily_rate ?? 0,
                'tax' => ($stay->daily_rate ?? 0) * 0.19, // 19% IVA
                'posted_by' => null, // System job
                'posted_at' => now(),
            ]);

            $openFolio->charges()->save($charge);

            // Update balance
            $openFolio->balance += ($charge->amount + $charge->tax);
            $openFolio->save();
        }
    }
}
