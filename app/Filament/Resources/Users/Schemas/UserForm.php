<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nome')
                    ->required()
                    ->string()
                    ->maxLength(100),
                TextInput::make('email')
                    ->label('E-mail')
                    ->email()
                    ->required()
                    ->string()
                    ->maxLength(120),
                TextInput::make('password')
                    ->label('Senha')
                    ->password()
                    ->string()
                    ->minLength(8)
                    ->maxLength(100)
                    ->dehydrateStateUsing(fn($state) => filled($state) ? bcrypt($state) : null)
                    ->required(fn($livewire) => $livewire instanceof \Filament\Resources\Pages\CreateRecord),
                Select::make('role')
                    ->label('Função')
                    ->options(function () {
                        $current = Auth::user();
                        $all = Role::query()->pluck('name', 'id')->toArray();
                        if ($current && method_exists($current, 'hasRole') && $current->hasRole('super-admin')) {
                            return $all;
                        }
                        return collect($all)->reject(fn($name) => $name === 'super-admin')->toArray();
                    })
                    ->searchable()
                    ->preload()
                    ->required()
                    ->afterStateUpdated(function ($state, $livewire) {
                        // No immediate action; syncing happens on save
                    })
                    ->dehydrateStateUsing(fn($state) => $state)
                    ->saveRelationshipsUsing(function ($component, $record, $state) {
                        // Substitui todas as roles pelo valor único escolhido
                        if ($state) {
                            $role = Role::find($state);
                            if ($role) {
                                // Guarda referência ao ator
                                $actor = Auth::user();
                                if ($role->name === 'super-admin' && (! $actor || ! $actor->hasRole('super-admin'))) {
                                    // Ignora tentativa indevida
                                    return;
                                }
                                $record->syncRoles([$role->name]);
                            }
                        } else {
                            // Se nada selecionado, remove todas (mantendo proteção de super-admin via Policy)
                            $record->syncRoles([]);
                        }
                    }),
            ]);
    }
}
