<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Solo administradores pueden acceder al módulo de usuarios
     */
    public function viewAny(User $auth): bool
    {
        return $auth->hasRole('administrador');
    }

    /**
     * Ver un usuario específico
     */
    public function view(User $auth, User $user): bool
    {
        return $auth->hasRole('administrador');
    }

    /**
     * Crear usuario (solo admin)
     */
    public function create(User $auth): bool
    {
        return $auth->hasRole('administrador');
    }

    /**
     * Actualizar usuario (solo admin)
     */
    public function update(User $auth, User $user): bool
    {
        return $auth->hasRole('administrador');
    }

    /**
     * Eliminar usuario (solo admin)
     */
    public function delete(User $auth, User $user): bool
    {
        return $auth->hasRole('administrador');
    }

    /**
     * Cambiar contraseña
     */
    public function changePassword(User $auth, User $user): bool
    {
        // El usuario puede cambiar su propia contraseña, o un admin puede cambiar cualquiera
        return $auth->id === $user->id || $auth->hasRole('administrador');
    }

    /**
     * Resetear contraseña (solo admin)
     */
    public function resetPassword(User $auth, User $user): bool
    {
        return $auth->hasRole('administrador');
    }

    /**
     * Cambiar estado de usuario (solo admin)
     */
    public function toggleStatus(User $auth, User $user): bool
    {
        return $auth->hasRole('administrador');
    }

    /**
     * Ver actividad de usuario (solo admin)
     */
    public function viewActivities(User $auth, User $user): bool
    {
        return $auth->hasRole('administrador');
    }

    /**
     * Ver sesiones activas (solo admin)
     */
    public function viewSessions(User $auth, User $user): bool
    {
        return $auth->hasRole('administrador');
    }

    /**
     * Cerrar sesión remota (solo admin)
     */
    public function logoutRemote(User $auth, User $user): bool
    {
        return $auth->hasRole('administrador');
    }
}
