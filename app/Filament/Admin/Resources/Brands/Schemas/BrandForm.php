<?php

namespace App\Filament\Admin\Resources\Brands\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class BrandForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('panels.brand_identity'))
                    ->description(__('panels.brand_identity_description'))
                    ->icon('heroicon-o-bookmark')
                    ->schema([
                        TextInput::make('name')
                            ->label(__('panels.brand_name'))
                            ->placeholder('ex: Apple, Samsung, LG')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->helperText(__('panels.brand_visible_helper')),
                    ])->columnSpanFull(),
            ]);
    }
}
