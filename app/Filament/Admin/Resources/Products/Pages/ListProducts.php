<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Products\Pages;

use App\DownloadCatalogPdfAction;
use App\Filament\Admin\Resources\Products\ProductResource;
use App\Models\Category;
use App\Models\Product;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Radio;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ListProducts extends ListRecords
{
    protected static string $resource = ProductResource::class;

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Tous')
                ->badge(fn (): int => Product::query()->count()),
            'active' => Tab::make('Actifs')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->where('status', 'active'))
                ->badge(fn (): int => Product::query()->where('status', 'active')->count())
                ->badgeColor('success'),
            'low_stock' => Tab::make('Stock faible (1–5)')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->where('status', 'active')->whereBetween('stock_quantity', [1, 5]))
                ->badge(fn (): int => Product::query()->where('status', 'active')->whereBetween('stock_quantity', [1, 5])->count())
                ->badgeColor('warning'),
            'out_of_stock' => Tab::make('En rupture')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->where('status', 'active')->where('stock_quantity', '<=', 0))
                ->badge(fn (): int => Product::query()->where('status', 'active')->where('stock_quantity', '<=', 0)->count())
                ->badgeColor('danger'),
            'inactive' => Tab::make('Inactifs')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->where('status', 'inactive'))
                ->badge(fn (): int => Product::query()->where('status', 'inactive')->count())
                ->badgeColor('gray'),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('export_inventory_csv')
                ->label('Exporter inventaire (CSV)')
                ->icon('heroicon-o-document-arrow-down')
                ->color('gray')
                ->action(fn (): StreamedResponse => $this->exportInventoryCsv()),
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

    protected function exportInventoryCsv(): StreamedResponse
    {
        $products = Product::query()
            ->with(['category', 'brand', 'supplier'])
            ->orderBy('name')
            ->get();

        return response()->streamDownload(function () use ($products): void {
            $handle = fopen('php://output', 'w');
            if ($handle === false) {
                return;
            }

            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, [
                'Code',
                'Nom',
                'Modèle',
                'Catégorie',
                'Marque',
                'Fournisseur',
                'Prix achat (XAF)',
                'Prix vente (XAF)',
                'Marge unitaire (XAF)',
                'Stock',
                'Valeur stock (XAF)',
                'Statut',
            ], ';');

            foreach ($products as $product) {
                $purchase = (float) $product->supplier_price;
                $selling = (float) $product->selling_price;
                $stock = (int) $product->stock_quantity;

                fputcsv($handle, [
                    $product->product_code ?? '—',
                    $product->name,
                    $product->model_number ?? '—',
                    $product->category?->name ?? '—',
                    $product->brand?->name ?? '—',
                    $product->supplier?->name ?? '—',
                    $purchase,
                    $selling,
                    $selling - $purchase,
                    $stock,
                    $purchase * $stock,
                    $product->status,
                ], ';');
            }

            fclose($handle);
        }, 'inventaire-produits-'.now()->format('Y-m-d').'.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
