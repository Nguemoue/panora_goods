<?php

namespace App\Filament\Admin\Resources\Categories\RelationManagers;

use App\Enums\SpecificationInputType;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class SpecificationRelationManager extends RelationManager
{
    protected static string $relationship = 'specifications';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('resources.relation_managers.specifications');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('panels.characteristic_name'))
                    ->maxLength(255)
                    ->required(),

                Select::make('input_type')->label(__('panels.input_method'))->options(SpecificationInputType::class)->required(),

                TextInput::make('measure')->label(__('panels.unit_of_measure'))->maxLength(20),

                TextInput::make('description')->label(__('validation.attributes.description'))->maxLength(255),

                TextEntry::make('created_at')
                    ->label(__('panels.created_date'))
                    ->dateTime(),

                TextEntry::make('updated_at')
                    ->label(__('messages.updated_at'))
                    ->dateTime(),
            ]);
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name')->label(__('panels.characteristic_name')),

                TextEntry::make('input_type')->label(__('panels.input_method')),

                TextEntry::make('measure')->label(__('panels.unit_of_measure')),

                TextEntry::make('description')->label(__('validation.attributes.description')),

                TextEntry::make('created_at')
                    ->label(__('panels.created_date'))
                    ->dateTime(),

                TextEntry::make('updated_at')
                    ->label(__('messages.updated_at'))
                    ->dateTime(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->label(__('panels.characteristic_name'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('input_type')->label(__('panels.input_method')),

                TextColumn::make('measure')->label(__('panels.unit_of_measure')),

                TextColumn::make('description')->label(__('validation.attributes.description')),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()->disabled(fn ($record) => $record->products()->exists()),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
