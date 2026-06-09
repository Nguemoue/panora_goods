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
                Section::make(__('panels.specification_definition'))
                    ->description(__('panels.specification_definition_description'))
                    ->icon('heroicon-o-variable')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label(__('panels.characteristic_name'))
                            ->required()
                            ->maxLength(255)
                            ->placeholder('ex: RAM, Stockage, Volume'),
                        Select::make('input_type')
                            ->label(__('panels.input_method'))
                            ->options([
                                'text' => __('panels.free_text'),
                                'number' => __('panels.numeric_value'),
                                'select' => __('panels.dropdown_selection'),
                            ])
                            ->default('text')
                            ->required(),
                        TextInput::make('measure')
                            ->label(__('panels.unit_of_measure'))
                            ->placeholder('ex: GB, L, TB, Watts')
                            ->helperText(__('panels.unit_optional_helper')),
                        TextInput::make('description')
                            ->label(__('panels.internal_note'))
                            ->placeholder(__('panels.category_description_placeholder'))
                            ->maxLength(255),
                    ])->columnSpanFull(),
            ]);
    }
}
