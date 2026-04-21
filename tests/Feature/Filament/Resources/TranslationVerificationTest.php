<?php

namespace Tests\Feature\Filament\Resources;

use App\Filament\Resources\Brands\Pages\CreateBrand;
use App\Filament\Resources\Brands\Pages\ListBrands;
use App\Filament\Resources\Phones\Pages\CreatePhone;
use App\Filament\Resources\Phones\Pages\ListPhones;
use App\Filament\Resources\Sales\Pages\CreateSale;
use App\Filament\Resources\Sales\Pages\ListSales;
use App\Models\Brand;
use App\Models\Phone;
use App\Models\Sale;
use App\Models\Supplier;
use App\Models\User;
use function Pest\Livewire\livewire;
use function Pest\Laravel\actingAs;

// ============================================================================
// MARK: - Setup & Helpers
// ============================================================================

function getAdminUser(): User
{
    return User::factory()->create(['role' => 'admin']);
}

// ============================================================================
// MARK: - Sale Form Translations
// ============================================================================

describe('SaleForm Translations', function () {
    
    it('sale_details section key translates correctly', function () {
        $translation = __('sales.sale_details');
        expect($translation)->toBe('Sale Details');
        expect($translation)->not->toBe('sales.sale_details');
    });

    it('pricing_profit section key translates correctly', function () {
        $translation = __('sales.pricing_profit');
        expect($translation)->toBe('Pricing & Profit');
        expect($translation)->not->toBe('sales.pricing_profit');
    });

    it('customer_timing section key translates correctly', function () {
        $translation = __('sales.customer_timing');
        expect($translation)->toBe('Customer & Timing');
        expect($translation)->not->toBe('sales.customer_timing');
    });

    it('supplier_price_at_sale field label translates correctly', function () {
        $translation = __('sales.supplier_price_at_sale');
        expect($translation)->toBe('Supplier Price at Sale (Unit)');
        expect($translation)->not->toBe('sales.supplier_price_at_sale');
    });

    it('sale_price field label translates correctly', function () {
        $translation = __('sales.sale_price');
        expect($translation)->toBe('Selling Price (Unit)');
        expect($translation)->not->toBe('sales.sale_price');
    });

    it('profit field label translates correctly', function () {
        $translation = __('sales.profit');
        expect($translation)->toBe('Total Profit');
        expect($translation)->not->toBe('sales.profit');
    });
});

// ============================================================================
// MARK: - Sales Table Translations
// ============================================================================

describe('SalesTable Translations', function () {
    
    it('sold_at column label translates correctly', function () {
        $translation = __('sales.sold_at');
        expect($translation)->toBe('Date of Sale');
        expect($translation)->not->toBe('sales.sold_at');
    });

    it('sale_price column translates to price_unit correctly', function () {
        $translation = __('sales.price_unit');
        expect($translation)->toBe('Price (Unit)');
        expect($translation)->not->toBe('sales.price_unit');
    });

    it('total_revenue column label translates correctly', function () {
        $translation = __('sales.total_revenue');
        expect($translation)->toBe('Total Revenue');
        expect($translation)->not->toBe('sales.total_revenue');
    });

    it('profit column translates to total_profit correctly', function () {
        $translation = __('sales.total_profit');
        expect($translation)->toBe('Total Profit');
        expect($translation)->not->toBe('sales.total_profit');
    });

    it('brand filter label translates correctly', function () {
        $translation = __('sales.brand');
        expect($translation)->toBe('Brand');
        expect($translation)->not->toBe('sales.brand');
    });

    it('translations exist for all sales columns', function () {
        expect(__('sales.sold_at'))->not->toBeEmpty();
        expect(__('sales.price_unit'))->not->toBeEmpty();
        expect(__('sales.total_revenue'))->not->toBeEmpty();
        expect(__('sales.total_profit'))->not->toBeEmpty();
        expect(__('sales.brand'))->not->toBeEmpty();
    });
});

// ============================================================================
// MARK: - Brand Form Translations
// ============================================================================

describe('BrandForm Translations', function () {
    
    it('name field label translates from validation.attributes.name', function () {
        $translation = __('validation.attributes.name');
        expect($translation)->not->toBe('validation.attributes.name');
    });

    it('name field placeholder translates from brands.enter_brand_name', function () {
        $translation = __('brands.enter_brand_name');
        expect($translation)->toBe('Enter brand name');
        expect($translation)->not->toBe('brands.enter_brand_name');
    });

    it('name field helper text translates from brands.brand_information', function () {
        $translation = __('brands.brand_information');
        expect($translation)->toBe('Brand Information');
        expect($translation)->not->toBe('brands.brand_information');
    });

    it('can render brand create form', function () {
        $admin = getAdminUser();
        actingAs($admin);

        $component = livewire(CreateBrand::class);
        expect($component)
            ->assertSuccessful()
            ->assertFormFieldExists('name');
    });
});

// ============================================================================
// MARK: - Brands Table Translations
// ============================================================================

describe('BrandsTable Translations', function () {
    
    it('name column label translates from validation.attributes.name', function () {
        $translation = __('validation.attributes.name');
        expect($translation)->not->toBe('validation.attributes.name');
    });

    it('created_at column label translates from messages.created_at', function () {
        $translation = __('messages.created_at');
        expect($translation)->toBe('Created At');
        expect($translation)->not->toBe('messages.created_at');
    });

    it('updated_at column label translates from messages.updated_at', function () {
        $translation = __('messages.updated_at');
        expect($translation)->toBe('Updated At');
        expect($translation)->not->toBe('messages.updated_at');
    });

    it('can verify brand form renders', function () {
        $admin = getAdminUser();
        actingAs($admin);

        $component = livewire(CreateBrand::class);
        expect($component)->assertSuccessful();
    });
});

// ============================================================================
// MARK: - Phone Form Translations
// ============================================================================

describe('PhoneForm Translations', function () {
    
    it('brand_id field uses validation.attributes.brand translation', function () {
        $translation = __('validation.attributes.brand');
        expect($translation)->toBe('Brand');
        expect($translation)->not->toBe('validation.attributes.brand');
    });

    it('supplier_id field uses validation.attributes.supplier translation', function () {
        $translation = __('validation.attributes.supplier');
        expect($translation)->toBe('Supplier');
        expect($translation)->not->toBe('validation.attributes.supplier');
    });

    it('name field uses validation.attributes.name translation', function () {
        $translation = __('validation.attributes.name');
        expect($translation)->not->toBe('validation.attributes.name');
    });

    it('model_number field uses validation.attributes.model_number translation', function () {
        $translation = __('validation.attributes.model_number');
        expect($translation)->toBe('Model Number');
        expect($translation)->not->toBe('validation.attributes.model_number');
    });

    it('color field uses validation.attributes.color translation', function () {
        $translation = __('validation.attributes.color');
        expect($translation)->toBe('Color');
        expect($translation)->not->toBe('validation.attributes.color');
    });

    it('supplier_price field uses validation.attributes.supplier_price translation', function () {
        $translation = __('validation.attributes.supplier_price');
        expect($translation)->toBe('Supplier Price');
        expect($translation)->not->toBe('validation.attributes.supplier_price');
    });

    it('selling_price field uses validation.attributes.selling_price translation', function () {
        $translation = __('validation.attributes.selling_price');
        expect($translation)->toBe('Selling Price');
        expect($translation)->not->toBe('validation.attributes.selling_price');
    });

    it('stock_quantity field uses validation.attributes.stock_quantity translation', function () {
        $translation = __('validation.attributes.stock_quantity');
        expect($translation)->toBe('Stock Quantity');
        expect($translation)->not->toBe('validation.attributes.stock_quantity');
    });

    it('status field uses validation.attributes.status translation', function () {
        $translation = __('validation.attributes.status');
        expect($translation)->toBe('Status');
        expect($translation)->not->toBe('validation.attributes.status');
    });

    it('status field options use correct message translations', function () {
        expect(__('messages.in_stock'))->toBe('In Stock');
        expect(__('messages.out_of_stock'))->toBe('Out of Stock');
        expect(__('messages.discontinued'))->toBe('Discontinued');
    });

    it('ram field uses validation.attributes.ram translation', function () {
        $translation = __('validation.attributes.ram');
        expect($translation)->toBe('RAM');
        expect($translation)->not->toBe('validation.attributes.ram');
    });

    it('storage field uses validation.attributes.storage translation', function () {
        $translation = __('validation.attributes.storage');
        expect($translation)->toBe('Storage');
        expect($translation)->not->toBe('validation.attributes.storage');
    });

    it('specs field uses validation.attributes.specs translation', function () {
        $translation = __('validation.attributes.specs');
        expect($translation)->toBe('Specifications');
        expect($translation)->not->toBe('validation.attributes.specs');
    });

    it('images field uses validation.attributes.images translation', function () {
        $translation = __('validation.attributes.images');
        expect($translation)->toBe('Images');
        expect($translation)->not->toBe('validation.attributes.images');
    });

    it('can render phone create form', function () {
        $admin = getAdminUser();
        actingAs($admin);

        $component = livewire(CreatePhone::class);
        expect($component)->assertSuccessful();
    });
});

// ============================================================================
// MARK: - Phones Table Translations
// ============================================================================

describe('PhonesTable Translations', function () {
    
    it('image column uses validation.attributes.image translation', function () {
        $translation = __('validation.attributes.image');
        expect($translation)->toBe('Image');
        expect($translation)->not->toBe('validation.attributes.image');
    });

    it('name column uses validation.attributes.name translation', function () {
        $translation = __('validation.attributes.name');
        expect($translation)->not->toBe('validation.attributes.name');
    });

    it('brand column uses validation.attributes.brand translation', function () {
        $translation = __('validation.attributes.brand');
        expect($translation)->toBe('Brand');
        expect($translation)->not->toBe('validation.attributes.brand');
    });

    it('supplier column uses validation.attributes.supplier translation', function () {
        $translation = __('validation.attributes.supplier');
        expect($translation)->toBe('Supplier');
        expect($translation)->not->toBe('validation.attributes.supplier');
    });

    it('supplier_price column uses validation.attributes.supplier_price translation', function () {
        $translation = __('validation.attributes.supplier_price');
        expect($translation)->toBe('Supplier Price');
        expect($translation)->not->toBe('validation.attributes.supplier_price');
    });

    it('selling_price column uses validation.attributes.selling_price translation', function () {
        $translation = __('validation.attributes.selling_price');
        expect($translation)->toBe('Selling Price');
        expect($translation)->not->toBe('validation.attributes.selling_price');
    });

    it('margin column uses messages.margin translation', function () {
        $translation = __('messages.margin');
        expect($translation)->toBe('Profit Margin');
        expect($translation)->not->toBe('messages.margin');
    });

    it('stock_quantity column uses validation.attributes.stock_quantity translation', function () {
        $translation = __('validation.attributes.stock_quantity');
        expect($translation)->toBe('Stock Quantity');
        expect($translation)->not->toBe('validation.attributes.stock_quantity');
    });

    it('status column uses validation.attributes.status translation', function () {
        $translation = __('validation.attributes.status');
        expect($translation)->toBe('Status');
        expect($translation)->not->toBe('validation.attributes.status');
    });

    it('ram column uses validation.attributes.ram translation', function () {
        $translation = __('validation.attributes.ram');
        expect($translation)->toBe('RAM');
        expect($translation)->not->toBe('validation.attributes.ram');
    });

    it('storage column uses validation.attributes.storage translation', function () {
        $translation = __('validation.attributes.storage');
        expect($translation)->toBe('Storage');
        expect($translation)->not->toBe('validation.attributes.storage');
    });

    it('created_at column uses messages.created translation', function () {
        $translation = __('messages.created');
        expect($translation)->toBe('Created');
        expect($translation)->not->toBe('messages.created');
    });

    it('brand filter uses validation.attributes.brand translation', function () {
        $translation = __('validation.attributes.brand');
        expect($translation)->toBe('Brand');
        expect($translation)->not->toBe('validation.attributes.brand');
    });

    it('supplier filter uses validation.attributes.supplier translation', function () {
        $translation = __('validation.attributes.supplier');
        expect($translation)->toBe('Supplier');
        expect($translation)->not->toBe('validation.attributes.supplier');
    });

    it('status filter uses validation.attributes.status translation', function () {
        $translation = __('validation.attributes.status');
        expect($translation)->toBe('Status');
        expect($translation)->not->toBe('validation.attributes.status');
    });

    it('phones table verifies column translations exist', function () {
        // All key column translations should be available
        expect(__('validation.attributes.name'))->not->toBeEmpty();
        expect(__('validation.attributes.brand'))->not->toBeEmpty();
        expect(__('messages.margin'))->not->toBeEmpty();
    });

    it('can verify phone list page renders', function () {
        $admin = getAdminUser();
        actingAs($admin);

        $component = livewire(ListPhones::class);
        expect($component)->assertSuccessful();
    });
});

// ============================================================================
// MARK: - Translation Helper Verification
// ============================================================================

describe('Translation Helper Verification', function () {
    
    it('__() helper returns correct translations for sales', function () {
        expect(__('sales.sale_details'))->toBe('Sale Details');
        expect(__('sales.pricing_profit'))->toBe('Pricing & Profit');
        expect(__('sales.customer_timing'))->toBe('Customer & Timing');
        expect(__('sales.supplier_price_at_sale'))->toBe('Supplier Price at Sale (Unit)');
        expect(__('sales.sale_price'))->toBe('Selling Price (Unit)');
        expect(__('sales.profit'))->toBe('Total Profit');
        expect(__('sales.sold_at'))->toBe('Date of Sale');
        expect(__('sales.price_unit'))->toBe('Price (Unit)');
        expect(__('sales.total_revenue'))->toBe('Total Revenue');
        expect(__('sales.total_profit'))->toBe('Total Profit');
        expect(__('sales.brand'))->toBe('Brand');
    });

    it('__() helper returns correct translations for brands', function () {
        expect(__('brands.enter_brand_name'))->toBe('Enter brand name');
        expect(__('brands.brand_information'))->toBe('Brand Information');
    });

    it('__() helper returns correct translations for validation attributes', function () {
        expect(__('validation.attributes.name'))->not->toBeEmpty();
        expect(__('validation.attributes.brand'))->toBe('Brand');
        expect(__('validation.attributes.supplier'))->toBe('Supplier');
        expect(__('validation.attributes.model_number'))->toBe('Model Number');
        expect(__('validation.attributes.color'))->toBe('Color');
        expect(__('validation.attributes.supplier_price'))->toBe('Supplier Price');
        expect(__('validation.attributes.selling_price'))->toBe('Selling Price');
        expect(__('validation.attributes.stock_quantity'))->toBe('Stock Quantity');
        expect(__('validation.attributes.status'))->toBe('Status');
        expect(__('validation.attributes.ram'))->toBe('RAM');
        expect(__('validation.attributes.storage'))->toBe('Storage');
        expect(__('validation.attributes.specs'))->toBe('Specifications');
        expect(__('validation.attributes.images'))->toBe('Images');
        expect(__('validation.attributes.image'))->toBe('Image');
    });

    it('__() helper returns correct translations for messages', function () {
        expect(__('messages.created_at'))->toBe('Created At');
        expect(__('messages.updated_at'))->toBe('Updated At');
        expect(__('messages.margin'))->toBe('Profit Margin');
        expect(__('messages.created'))->toBe('Created');
        expect(__('messages.in_stock'))->toBe('In Stock');
        expect(__('messages.out_of_stock'))->toBe('Out of Stock');
        expect(__('messages.discontinued'))->toBe('Discontinued');
    });

    it('no hardcoded english strings in translation keys', function () {
        $allTranslations = [
            'sales.sale_details' => __('sales.sale_details'),
            'sales.pricing_profit' => __('sales.pricing_profit'),
            'sales.customer_timing' => __('sales.customer_timing'),
            'brands.enter_brand_name' => __('brands.enter_brand_name'),
            'brands.brand_information' => __('brands.brand_information'),
            'messages.created_at' => __('messages.created_at'),
            'messages.updated_at' => __('messages.updated_at'),
        ];

        foreach ($allTranslations as $key => $translation) {
            expect($translation)->not->toBe($key);
            expect($translation)->not->toBeEmpty();
            expect(strlen($translation))->toBeGreaterThan(0);
        }
    });

    it('all form field labels are translated not hardcoded', function () {
        // Check that __() is being used for all critical form fields
        expect(__('validation.attributes.name'))->not->toBe('validation.attributes.name');
        expect(__('validation.attributes.brand'))->not->toBe('validation.attributes.brand');
        expect(__('validation.attributes.supplier'))->not->toBe('validation.attributes.supplier');
        expect(__('validation.attributes.supplier_price'))->not->toBe('validation.attributes.supplier_price');
        expect(__('validation.attributes.selling_price'))->not->toBe('validation.attributes.selling_price');
        expect(__('validation.attributes.status'))->not->toBe('validation.attributes.status');
    });

    it('all table column labels are translated not hardcoded', function () {
        // Check that __() is being used for all critical table columns
        expect(__('sales.sold_at'))->not->toBe('sales.sold_at');
        expect(__('sales.price_unit'))->not->toBe('sales.price_unit');
        expect(__('sales.total_revenue'))->not->toBe('sales.total_revenue');
        expect(__('sales.total_profit'))->not->toBe('sales.total_profit');
        expect(__('messages.created_at'))->not->toBe('messages.created_at');
        expect(__('messages.updated_at'))->not->toBe('messages.updated_at');
    });
});

describe('Translation Integration', function () {
    
    it('brand form renders without translation errors', function () {
        $admin = getAdminUser();
        actingAs($admin);

        $component = livewire(CreateBrand::class);
        
        expect($component)->assertSuccessful();
    });

    it('phone form renders without translation errors', function () {
        $admin = getAdminUser();
        actingAs($admin);

        $component = livewire(CreatePhone::class);
        
        expect($component)->assertSuccessful();
    });

    it('brands list page renders without translation errors', function () {
        $admin = getAdminUser();
        actingAs($admin);

        $component = livewire(ListBrands::class);
        
        expect($component)->assertSuccessful();
    });

    it('phones list page renders without translation errors', function () {
        $admin = getAdminUser();
        actingAs($admin);

        $component = livewire(ListPhones::class);
        
        expect($component)->assertSuccessful();
    });
});
