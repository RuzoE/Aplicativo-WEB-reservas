<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(\App\Services\AuditoriaService::class, function () {
            return new \App\Services\AuditoriaService();
        });
    }

    public function boot(): void
    {
        $certificateBundle = $this->configureCertificateBundle();
        $this->registerGoogleDriveFilesystem($certificateBundle);

        Schema::defaultStringLength(191);
        Paginator::useBootstrapFive();

        View::composer('admin.*', function ($view) {
            $view->with('adminView', true);
        });

        View::composer('admin.sidebar', function ($view) {
            $anticiposCount = \App\Models\Order::whereIn('status', [\App\Models\Order::STATUS_ANTICIPO_PAGADO, 'confirmada'])
                ->where('down_payment_amount', '>', 0)
                ->where('is_paid', true)
                ->count();
            $view->with('anticiposCount', $anticiposCount);
        });

        // Blade directive to format prices in Colombian Pesos (COP)
        // Usage: @cop($price) or @cop($price, $days)
        Blade::directive('cop', function ($expression) {
            // Allow optional days parameter: @cop(price) or @cop(price, days)
            return "<?php \n"
                . "\t\$__cop_args = [$expression];\n"
                . "\t\$__cop_price = (float)(\$__cop_args[0] ?? 0);\n"
                . "\t\$__cop_days = (int)(\$__cop_args[1] ?? 1);\n"
                . "\t\$__cop_total = \$__cop_price * max(1, \$__cop_days);\n"
                . "\techo '$' . number_format(\$__cop_total, 0, ',', '.');\n"
                . "?>";
        });

        // Desactiva la Debugbar solo en /admin/minibar/bebidas*
        if (request()->is('admin/minibar/bebidas*') && app()->bound('debugbar')) {
            app('debugbar')->disable();
        }

        if (filter_var(env('APP_FORCE_HTTPS', false), FILTER_VALIDATE_BOOL)) {
            $this->app['request']->server->set('HTTPS','on');
            URL::forceScheme('https');
        }
    }

    protected function configureCertificateBundle(): ?string
    {
        $certificateBundle = env('CA_BUNDLE_PATH');

        if (! $certificateBundle && is_file('C:/laragon/etc/ssl/cacert.pem')) {
            $certificateBundle = 'C:/laragon/etc/ssl/cacert.pem';
        }

        if (! $certificateBundle || ! is_file($certificateBundle)) {
            return null;
        }

        putenv("SSL_CERT_FILE={$certificateBundle}");
        putenv("CURL_CA_BUNDLE={$certificateBundle}");
        ini_set('openssl.cafile', $certificateBundle);
        ini_set('curl.cainfo', $certificateBundle);

        return $certificateBundle;
    }

    protected function registerGoogleDriveFilesystem(?string $certificateBundle): void
    {
        Storage::extend('google', function ($app, array $config) use ($certificateBundle) {
            $options = [];

            if (! empty($config['teamDriveId'] ?? null)) {
                $options['teamDriveId'] = $config['teamDriveId'];
            }

            $client = new \Google\Client;
            $client->setClientId($config['clientId']);
            $client->setClientSecret($config['clientSecret']);

            if ($certificateBundle) {
                $client->setHttpClient(new \GuzzleHttp\Client([
                    'verify' => $certificateBundle,
                ]));
            }

            $client->refreshToken($config['refreshToken']);

            if (! empty($config['accessToken'])) {
                $client->setAccessToken($config['accessToken']);
            }

            $service = new \Google\Service\Drive($client);
            $adapter = new \Masbug\Flysystem\GoogleDriveAdapter($service, $config['folder'] ?? '/', $options);
            $driver = new \League\Flysystem\Filesystem($adapter);

            return new \Illuminate\Filesystem\FilesystemAdapter($driver, $adapter);
        });
    }
}
