<?php

namespace App\Filament\Seller\Widgets\Seller;

use App\Filament\Seller\Resources\Products\ProductResource;
use App\Filament\Seller\Resources\Sales\SaleResource;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Widgets\Widget;

class SellerQuickActions extends Widget implements HasForms, HasActions
{
    use InteractsWithForms;
    use InteractsWithActions;

    protected  string $view = 'filament.seller.widgets.seller.seller-quick-actions';
    protected int | string | array $columnSpan = 'full';

    public function registerSaleAction(): Action
    {
        return Action::make('registerSale')
            ->label('Register a Sale')
            ->icon('heroicon-o-shopping-cart')
            ->color('success')
            ->url(SaleResource::getUrl('create'));
    }

    public function browseCatalogAction(): Action
    {
        return Action::make('browseCatalog')
            ->label('Product Catalog')
            ->icon('heroicon-o-rectangle-stack')
            ->color('info')
            ->url(ProductResource::getUrl('index'));
    }
}
