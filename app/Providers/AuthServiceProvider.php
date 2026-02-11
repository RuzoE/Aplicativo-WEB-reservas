<?php

namespace App\Providers;

use App\Models\Room;
use App\Models\RoomType;
use App\Models\Stay;
use App\Models\Folio;
use App\Policies\RoomPolicy;
use App\Policies\RoomTypePolicy;
use App\Policies\StayPolicy;
use App\Policies\FolioPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Contracts\Auth\Authenticatable;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Room::class     => RoomPolicy::class,
        RoomType::class => RoomTypePolicy::class,
        Stay::class     => StayPolicy::class,
        Folio::class    => FolioPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Bypass total para el rol admin (Spatie)
        Gate::before(function (?Authenticatable $user, string $ability) {
            // si no hay usuario autenticado, no decide
            if (!$user) return null;

            // hasRole() es de Spatie (asegúrate de usar HasRoles en el modelo User)
            return method_exists($user, 'hasRole') && $user->hasRole('admin') ? true : null;
        });
    }
}
