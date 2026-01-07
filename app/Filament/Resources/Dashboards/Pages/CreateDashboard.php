<?php

namespace App\Filament\Resources\Dashboards\Pages;

use App\Filament\Resources\Dashboards\DashboardResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Validation\ValidationException;
use App\Models\Dashboard;
use App\Models\Report;
use App\Models\Organization;

class CreateDashboard extends CreateRecord
{
    protected static string $resource = DashboardResource::class;

    protected function beforeCreate(array $data = []): void
    {
        // Se for dashboard, validar unicidade apenas quando a organização estiver configurada como single_dashboard
        if (($data['type'] ?? 'dashboard') === 'dashboard' && ! empty($data['organization_id'])) {
            $org = Organization::find($data['organization_id']);
            if ($org && (bool) $org->single_dashboard) {
                $exists = Dashboard::where('organization_id', $data['organization_id'])->exists();
                if ($exists) {
                    throw ValidationException::withMessages([
                        'organization_id' => 'Esta organização está configurada para ter apenas um dashboard e já possui um criado.'
                    ]);
                }
            }
        }
    }

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        // Garantir que o tipo tenha um valor padrão
        $data['type'] = $data['type'] ?? 'dashboard';
        
        // Criar no modelo correto baseado no tipo
        if ($data['type'] === 'report') {
            return Report::create($data);
        } else {
            return Dashboard::create($data);
        }
    }

    protected function getRedirectUrl(): string
    {
        $record = $this->getRecord();
        
        // Redirecionar para a lista correta baseada no tipo
        if ($record->getTable() === 'reports') {
            // Evita hardcode do ID do painel e do slug
            return \App\Filament\Resources\Reports\ReportResource::getUrl('index');
        }

        return $this->getResource()::getUrl('index');
    }
}
