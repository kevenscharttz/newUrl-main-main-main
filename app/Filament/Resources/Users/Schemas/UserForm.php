<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;
use Spatie\Permission\Models\Role;

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
                Select::make('roles')
                    ->label('Funções')
                    ->multiple()
                    ->relationship('roles', 'name')
                    ->preload()
                    ->required(),
            ]);
    }
}
