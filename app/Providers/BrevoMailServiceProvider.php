<?php

namespace App\Providers;

use App\Mail\Transport\BrevoTransport;
use Illuminate\Mail\MailManager;
use Illuminate\Support\ServiceProvider;

class BrevoMailServiceProvider extends ServiceProvider
{
    /**
     * Register the Brevo mail transport.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap the Brevo mail transport.
     */
    public function boot(): void
    {
        $this->registerBrevoTransport();
    }

    /**
     * Register the Brevo transport with Laravel's mail manager.
     */
    protected function registerBrevoTransport(): void
    {
        $this->app->make('mail.manager')->extend('brevo', function () {
            return new BrevoTransport(
                apiKey: config('services.brevo.api_key')
            );
        });
    }
}
