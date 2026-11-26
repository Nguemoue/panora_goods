<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Products\Pages;

use App\Filament\Admin\Resources\Products\ProductResource;
use App\Filament\Admin\Widgets\Admin\ProductSalesChart;
use App\Models\Product;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('view_storefront')
                ->label('Voir sur la boutique')
                ->icon('heroicon-o-arrow-top-right-on-square')
                ->color('gray')
                ->url(fn (Product $record): string => route('products.show', $record))
                ->openUrlInNewTab(),
            Action::make('duplicate_product')
                ->label('Dupliquer')
                ->icon('heroicon-o-document-duplicate')
                ->color('info')
                ->requiresConfirmation()
                ->modalHeading('Dupliquer ce produit')
                ->modalDescription('Une copie inactive (stock à 0) sera créée avec toutes ses photos et spécifications.')
                ->action(function (Product $record) {
                    $copy = $record->duplicateWithRelations();

                    Notification::make()
                        ->title('Produit dupliqué')
                        ->body('Ajustez le nom, le prix et le stock avant de l\'activer.')
                        ->success()
                        ->send();

                    return redirect()->to(ProductResource::getUrl('edit', ['record' => $copy]));
                }),
            DeleteAction::make()
                ->visible(fn (Product $record): bool => $record->canBeDeleted()),
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            ProductSalesChart::class,
        ];
    }
}
