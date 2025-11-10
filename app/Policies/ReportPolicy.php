<?php

namespace App\Policies;

use App\Models\Report;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ReportPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Super-admin pode tudo
        if ($user->hasRole('super-admin')) {
            return true;
        }
        // Organization-manager e usuários podem listar relatórios
        return $user->hasAnyRole(['organization-manager', 'user']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Report $report): bool
    {
        // Super-admin pode ver tudo
        if ($user->hasRole('super-admin')) {
            return true;
        }

        // Organization-manager pode ver relatórios da sua organização
        if ($user->hasRole('organization-manager')) {
            return $user->organizations()->where('organizations.id', $report->organization_id)->exists();
        }

        // Usuários podem ver relatórios das organizações que pertencem
        return $user->organizations()->where('organizations.id', $report->organization_id)->exists();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Apenas super-admin (capturado pelo Gate::before) e organization-manager podem criar
        return $user->hasRole('organization-manager');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Report $report): bool
    {
        // Super-admin pode editar tudo
        if ($user->hasRole('super-admin')) {
            return true;
        }

        // Apenas Organization-manager pode editar relatórios da sua organização
        if ($user->hasRole('organization-manager')) {
            return $user->organizations()->where('organizations.id', $report->organization_id)->exists();
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Report $report): bool
    {
        // Super-admin pode deletar tudo
        if ($user->hasRole('super-admin')) {
            return true;
        }

        // Organization-manager pode deletar relatórios da sua organização
        if ($user->hasRole('organization-manager')) {
            return $user->organizations()->where('organizations.id', $report->organization_id)->exists();
        }

        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Report $report): bool
    {
        return $user->hasRole('super-admin');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Report $report): bool
    {
        return $user->hasRole('super-admin');
    }
}
