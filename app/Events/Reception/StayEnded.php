<?php

namespace App\Events\Reception;

use App\Models\Stay;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StayEnded
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Stay $stay
    ) {}
}
