<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        View::composer('admin.*', function ($view) {
            $view->with('adminView', true);
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

        if ($this->app->environment('production')) {
            $this->app['request']->server->set('HTTPS','on');
            URL::forceScheme('https');
        }
    }
}
