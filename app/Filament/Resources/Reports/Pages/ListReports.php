<?php

namespace App\Filament\Resources\Reports\Pages;

use App\Filament\Resources\Reports\ReportResource;
use App\Models\Report;
use App\Models\User;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\CreateAction;

class ListReports extends ListRecords
{
    protected static string $resource = ReportResource::class;

    protected function getTableExtraAttributes(): array
    {
        return [
            'style' => 'width: 100%; max-width: none;',
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public static function getEloquentQuery()
    {
        $query = parent::getEloquentQuery();
        $user = \Filament\Facades\Filament::auth()?->user();
        
        if ($user instanceof User) {
            // Normalize super-admin role name checks
            $isSuper = $user->hasRole('super-admin') || $user->hasRole('super_admin');

            // Super-admin pode ver todos os relatórios
            if ($isSuper) {
                return $query;
            }

            // Organization-manager e usuários só veem relatórios da sua organização
            $orgIds = $user->organizations()->pluck('id');
            $query = $query->whereIn('organization_id', $orgIds);
        }
        
        return $query;
    }
}
