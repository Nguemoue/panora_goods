<?php

use App\Filament\Admin\Resources\Brands\BrandResource;
use App\Filament\Admin\Resources\Categories\CategoryResource;
use App\Filament\Admin\Resources\Clients\ClientResource;
use App\Filament\Admin\Resources\Products\ProductResource as AdminProductResource;
use App\Filament\Admin\Resources\Sales\RelationManagers\SalePaymentRelationManager as AdminSalePaymentRelationManager;
use App\Filament\Admin\Resources\Sales\SaleResource as AdminSaleResource;
use App\Filament\Admin\Resources\Specifications\SpecificationResource;
use App\Filament\Admin\Resources\Suppliers\SupplierResource;
use App\Filament\Admin\Resources\Users\UserResource;
use App\Filament\Admin\Resources\Zones\ZoneResource;
use App\Filament\Client\Resources\Products\ProductResource as ClientProductResource;
use App\Filament\Client\Resources\Sales\SaleResource as ClientSaleResource;
use App\Filament\Enums\AdminNavigationGroupEnum;
use App\Filament\Seller\Resources\Products\ProductResource as SellerProductResource;
use App\Filament\Seller\Resources\Sales\SaleResource as SellerSaleResource;
use App\Http\Middleware\SetFilamentLocale;
use App\Models\User;
use App\Providers\Filament\AdminPanelProvider;
use App\Providers\Filament\ClientPanelProvider;
use App\Providers\Filament\SellerPanelProvider;
use Filament\Panel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

it('uses french as the default application locale', function () {
    expect(config('app.locale'))->toBe('fr')
        ->and(config('app.fallback_locale'))->toBe('fr')
        ->and(config('app.faker_locale'))->toBe('fr_FR');
});

it('loads filament translations in french', function () {
    expect(__('filament-panels::auth/pages/login.title'))->toBe('Connexion')
        ->and(__('filament-panels::pages/dashboard.title'))->toBe('Tableau de bord')
        ->and(__('filament-actions::delete.single.label'))->toBe('Supprimer')
        ->and(__('filament-tables::table.fields.search.label'))->toBe('Rechercher');
});

it('loads application panel translations in french', function () {
    expect(__('panels.filters'))->toBe('Filtres')
        ->and(__('panels.product_wizard'))->toBe('Assistant produit')
        ->and(__('panels.transaction_header'))->toBe('Informations de la vente')
        ->and(__('panels.my_monthly_performance'))->toBe('Ma performance mensuelle')
        ->and(__('panels.supplier_contact'))->toBe('Contact fournisseur');
});

it('uses french labels for all filament resources and relation managers', function () {
    expect(AdminProductResource::getNavigationLabel())->toBe('Produits')
        ->and(AdminProductResource::getModelLabel())->toBe('produit')
        ->and(AdminProductResource::getPluralModelLabel())->toBe('produits')
        ->and(AdminSaleResource::getNavigationLabel())->toBe('Ventes')
        ->and(BrandResource::getNavigationLabel())->toBe('Marques')
        ->and(CategoryResource::getNavigationLabel())->toBe('Catégories')
        ->and(SpecificationResource::getNavigationLabel())->toBe('Spécifications')
        ->and(SupplierResource::getNavigationLabel())->toBe('Fournisseurs')
        ->and(ClientResource::getNavigationLabel())->toBe('Clients')
        ->and(UserResource::getNavigationLabel())->toBe('Employés')
        ->and(ZoneResource::getNavigationLabel())->toBe('Zones')
        ->and(SellerProductResource::getNavigationLabel())->toBe('Produits')
        ->and(SellerSaleResource::getNavigationLabel())->toBe('Ventes')
        ->and(ClientProductResource::getNavigationLabel())->toBe('Produits')
        ->and(ClientSaleResource::getNavigationLabel())->toBe('Ventes')
        ->and(AdminNavigationGroupEnum::PERSONNEL->getLabel())->toBe('Personnel')
        ->and(AdminSalePaymentRelationManager::getTitle(new User, ''))->toBe('Paiements de la vente');
});

it('registers the french locale middleware on every filament panel', function (string $providerClass) {
    $panel = (new $providerClass(app()))->panel(Panel::make());

    expect($panel->getMiddleware())->toContain(SetFilamentLocale::class);
})->with([
    AdminPanelProvider::class,
    SellerPanelProvider::class,
    ClientPanelProvider::class,
]);

it('forces french while serving a filament request', function () {
    App::setLocale('en');

    app(SetFilamentLocale::class)->handle(Request::create('/admin'), fn (): Response => response('ok'));

    expect(App::getLocale())->toBe('fr');
});
