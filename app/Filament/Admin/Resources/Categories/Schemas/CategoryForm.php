<?php

namespace App\Filament\Admin\Resources\Categories\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('panels.category_information'))
                    ->description(__('panels.category_information_description'))
                    ->icon('heroicon-o-tag')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->columnSpanFull()
                            ->label(__('panels.category_name'))
                            ->placeholder('ex: Smartphones, Réfrigérateurs')
                            ->required()
                            ->columnSpanFull()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->columnSpan(1),
                        Textarea::make('description')
                            ->label(__('panels.brief_description'))
                            ->placeholder(__('panels.category_description_placeholder'))
                            ->rows(3)
                            ->maxLength(1000)
                            ->columnSpanFull(),
                    ])->columnSpanFull(),
            ]);
    }
}
