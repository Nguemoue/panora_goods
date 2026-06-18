<?php

namespace App\Filament\Admin\Resources\Products\Pages;

use App\DownloadCatalogPdfAction;
use App\Filament\Admin\Resources\Products\ProductResource;
use App\Models\Category;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Radio;
use Filament\Resources\Pages\ListRecords;

class ListProducts extends ListRecords
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('download_catalog')
                ->label(__('panels.download_catalog'))
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->modalHeading(__('panels.download_catalog'))
                ->modalSubmitActionLabel(__('panels.download_pdf'))
                ->schema([
                    CheckboxList::make('category_ids')
                        ->label(__('panels.catalog_categories'))
                        ->helperText(__('panels.catalog_categories_helper'))
                        ->options(fn (): array => Category::query()
                            ->orderBy('name')
                            ->pluck('name', 'id')
                            ->all())
                        ->default(fn (): array => Category::query()
                            ->orderBy('name')
                            ->pluck('id')
                            ->map(fn (int $id): string => (string) $id)
                            ->all())
                        ->bulkToggleable()
                        ->columns(2)
                        ->required(),
                    Radio::make('columns')
                        ->label(__('panels.catalog_cards_per_row'))
                        ->options([
                            2 => __('panels.catalog_two_per_row'),
                            3 => __('panels.catalog_three_per_row'),
                        ])
                        ->default(3)
                        ->inline()
                        ->required(),
                ])
                ->action(fn (array $data) => app(DownloadCatalogPdfAction::class)->handle(
                    $data['category_ids'] ?? [],
                    (int) ($data['columns'] ?? 3),
                )),
            CreateAction::make(),
        ];
    }
}
