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
                Section::make('Brand Identity')
                    ->description('Manage manufacturer names for your catalog.')
                    ->icon('heroicon-o-bookmark')
                    ->schema([
                        TextInput::make('name')
                            ->label('Brand Name')
                            ->placeholder('e.g., Apple, Samsung, LG')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->helperText('This name will be visible to sellers and clients.'),
                    ])->columnSpanFull(),
            ]);
    }
}
