<?php

namespace App\Filament\Admin\Resources\Specifications\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class SpecificationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Specification Definition')
                    ->description('Define a technical characteristic that can be linked to categories.')
                    ->icon('heroicon-o-variable')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Characteristic Name')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('e.g., RAM, Storage, Volume'),
                        Select::make('input_type')
                            ->label('Input Method')
                            ->options([
                                'text' => 'Free Text',
                                'number' => 'Numeric Value',
                                'select' => 'Dropdown Selection',
                            ])
                            ->default('text')
                            ->required(),
                        TextInput::make('measure')
                            ->label('Unit of Measure')
                            ->placeholder('e.g., GB, L, TB, Watts')
                            ->helperText('Leave empty if no unit is applicable.'),
                        TextInput::make('description')
                            ->label('Internal Note')
                            ->placeholder('Describe what this spec is for...')
                            ->maxLength(255),
                    ])->columnSpanFull(),
            ]);
    }
}
