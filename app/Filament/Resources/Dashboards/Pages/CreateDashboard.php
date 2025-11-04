<?php

namespace App\Filament\Resources\Dashboards\Pages;

use App\Filament\Resources\Dashboards\DashboardResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Validation\ValidationException;
use App\Models\Dashboard;
use App\Models\Report;

class CreateDashboard extends CreateRecord
{
    protected static string $resource = DashboardResource::class;

    protected function beforeCreate(array $data = []): void
    {
        // Se for dashboard, validar unicidade
        if (isset($data['type']) && $data['type'] === 'dashboard' && ! empty($data['organization_id'])) {
            $exists = Dashboard::where('organization_id', $data['organization_id'])->exists();
            if ($exists) {
                throw ValidationException::withMessages(['organization_id' => 'Já existe um Dashboard para essa organização.']);
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
            return route('filament.admin.resources.reports.index');
        } else {
            return $this->getResource()::getUrl('index');
        }
    }
}
