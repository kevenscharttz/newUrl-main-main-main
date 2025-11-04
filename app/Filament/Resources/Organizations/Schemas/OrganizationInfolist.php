<?php

namespace App\Filament\Resources\Organizations\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Schemas\Schema;

class OrganizationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name')
                    ->label('Nome da Organização')
                    ->weight('bold')
                    ->size('xl')
                    ->color('primary')
                    ->columnSpanFull(),

                TextEntry::make('logo_url')
                    ->label('Logo')
                    ->formatStateUsing(function ($state, $record) {
                        if (!$state) {
                            return '<div class="flex items-center justify-center bg-gray-100 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm p-8">
                                        <div class="text-center text-gray-500 dark:text-gray-400">
                                            <svg class="mx-auto h-12 w-12 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                            </svg>
                                            <p class="text-sm font-medium">Sem Logo</p>
                                        </div>
                                    </div>';
                        }
                        
                        $exists = \Illuminate\Support\Facades\Storage::disk('public')->exists($state);
                        if (!$exists) {
                            return '<div class="text-red-500">Arquivo de logo não encontrado</div>';
                        }
                        
                        $url = \Illuminate\Support\Facades\Storage::disk('public')->url($state);
                        $alt = $record->logo_alt_text ?? $record->name;
                        $width = $record->logo_settings['width'] ?? 200;
                        $height = $record->logo_settings['height'] ?? 100;
                        
                        return '<img src="' . $url . '" alt="' . htmlspecialchars($alt) . '" class="rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm" style="max-width: ' . $width . 'px; max-height: ' . $height . 'px; width: auto; height: auto;" />';
                    })
                    ->html()
                    ->columnSpanFull(),

                TextEntry::make('slug')
                    ->label('Identificador')
                    ->badge()
                    ->color('gray')
                    ->copyable()
                    ->copyMessage('Slug copiado!'),

                TextEntry::make('single_dashboard')
                    ->label('Configuração de Dashboard')
                    ->formatStateUsing(fn ($state) => $state ? 'Apenas um dashboard' : 'Múltiplos dashboards')
                    ->badge()
                    ->color(fn ($state) => $state ? 'warning' : 'success'),

                TextEntry::make('description')
                    ->label('Descrição')
                    ->placeholder('Nenhuma descrição fornecida')
                    ->columnSpanFull()
                    ->markdown()
                    ->prose(),

                TextEntry::make('users_count')
                    ->label('Total de Usuários')
                    ->formatStateUsing(fn ($record) => $record->users()->count())
                    ->badge()
                    ->color('blue')
                    ->icon('heroicon-o-users'),

                TextEntry::make('dashboards_count')
                    ->label('Total de Dashboards')
                    ->formatStateUsing(fn ($record) => $record->dashboards()->count())
                    ->badge()
                    ->color('green')
                    ->icon('heroicon-o-chart-bar-square'),

                TextEntry::make('reports_count')
                    ->label('Total de Relatórios')
                    ->formatStateUsing(fn ($record) => $record->reports()->count())
                    ->badge()
                    ->color('purple')
                    ->icon('heroicon-o-document-text'),

                TextEntry::make('creator.name')
                    ->label('Criado por')
                    ->placeholder('Sistema')
                    ->badge()
                    ->color('green')
                    ->icon('heroicon-o-user'),

                TextEntry::make('created_at')
                    ->label('Data de Criação')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('-')
                    ->icon('heroicon-o-calendar'),

                TextEntry::make('updated_at')
                    ->label('Última Atualização')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('-')
                    ->icon('heroicon-o-clock'),
            ]);
    }
}
