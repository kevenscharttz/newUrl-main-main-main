<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
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

    // Relatórios podem ter múltiplos por organização (sem regra de unicidade)

    // 🔗 Relacionamentos
    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Scope reports visible to a given user.
     * Usage: Report::visibleTo($user)->get();
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
        // Apenas relatórios da(s) organização(ões) do usuário
        return $query->whereIn('organization_id', $organizationIds);
    }
}
