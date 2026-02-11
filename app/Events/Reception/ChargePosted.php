<?php

namespace App\Events\Reception;

use App\Models\Charge;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChargePosted
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Charge $charge
    ) {}
}
