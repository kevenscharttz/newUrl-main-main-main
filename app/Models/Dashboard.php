<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dashboard extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'organization_id',
        'type',
        'platform',
        'platform_custom',
        'url',
        'visibility',
        'scope_user_id',
        'scope_profile_id',
        'scope_organization_id',
        'tags',
        'settings',
        'description',
    ];

    protected $casts = [
        'settings' => 'array',
        'tags' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($dashboard) {
            // Enforce single dashboard only when organization is configured as single_dashboard
            $organization = Organization::find($dashboard->organization_id);
            if ($organization && (bool) $organization->single_dashboard) {
                $exists = static::where('organization_id', $dashboard->organization_id)->exists();
                if ($exists) {
                    throw new \Illuminate\Validation\ValidationException(
                        validator([], []),
                        ['organization_id' => ['Esta organização está configurada para ter apenas um dashboard e já possui um criado.']]
                    );
                }
            }
        });

        static::updating(function ($dashboard) {
            // Enforce single dashboard only when organization is configured as single_dashboard
            $organization = Organization::find($dashboard->organization_id);
            if ($organization && (bool) $organization->single_dashboard) {
                $exists = static::where('organization_id', $dashboard->organization_id)
                    ->where('id', '!=', $dashboard->id)
                    ->exists();
                if ($exists) {
                    throw new \Illuminate\Validation\ValidationException(
                        validator([], []),
                        ['organization_id' => ['Esta organização está configurada para ter apenas um dashboard e já possui outro criado.']]
                    );
                }
            }
        });
    }

    // 🔗 Relacionamentos
    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Scope dashboards visible to a given user.
     * Usage: Dashboard::visibleTo($user)->get();
     */
    public function scopeVisibleTo($query, $user)
    {
        if (! $user) {
            // guests não podem ver nada
            return $query->whereRaw('0=1');
        }

        // Super-admins veem tudo
        $isSuper = false;
        if (method_exists($user, 'getRoleNames')) {
            $roles = $user->getRoleNames()->toArray();
            foreach ($roles as $r) {
                $normalized = strtolower(preg_replace('/[^a-z0-9]/', '', $r));
                if (in_array($normalized, ['superadmin', 'super'], true)) {
                    $isSuper = true;
                    break;
                }
            }
        }

        if ($isSuper) {
            return $query;
        }

        $organizationIds = $user->organizations()->pluck('organizations.id')->toArray();
        // Apenas dashboards da(s) organização(ões) do usuário
        return $query->whereIn('organization_id', $organizationIds);
    }
}
