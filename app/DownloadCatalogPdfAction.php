<?php

namespace App;

use App\Models\Category;
use App\Models\Product;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DownloadCatalogPdfAction
{
    /**
     * @param  array<int|string>  $categoryIds
     */
    public function handle(array $categoryIds = [], int $columns = 3): StreamedResponse
    {
        $categories = $this->catalogData($categoryIds);
        $columns = in_array($columns, [2, 3], true) ? $columns : 3;

        $pdf = Pdf::loadView('pdf.catalog', [
            'categories' => $categories,
            'columns' => $columns,
            'generatedAt' => now(),
        ])->setPaper('a4');

        return response()->streamDownload(function () use ($pdf): void {
            echo $pdf->output();
        }, 'catalogue-produits-'.now()->format('Y-m-d-His').'.pdf');
    }

    /**
     * @param  array<int|string>  $categoryIds
     * @return Collection<int, Category>
     */
    public function catalogData(array $categoryIds = []): Collection
    {
        $categoryIds = collect($categoryIds)
            ->filter()
            ->values()
            ->all();

        $query = Category::query()
            ->with([
                'products' => fn ($query) => $query
                    ->with(['brand', 'primaryImage', 'specifications'])
                    ->orderBy('name'),
            ])
            ->orderBy('name');

        if ($categoryIds !== []) {
            $query->whereKey($categoryIds);
        }

        /** @var Collection<int, Category> $categories */
        $categories = $query->get();

        $categories->each(function (Category $category): void {
            $category->products->each(function (Product $product): void {
                $product->setAttribute('catalog_image_src', $this->imageSource($product));
            });
        });

        return $categories;
    }

    public function imageSource(Product $product): ?string
    {
        $path = $product->primaryImage?->path;

        if ($path && Storage::disk('public')->exists($path)) {
            return $this->fileToDataUri(Storage::disk('public')->path($path));
        }

        $placeholder = public_path('images/cart-placeholder.jpg');

        if (file_exists($placeholder)) {
            return $this->fileToDataUri($placeholder);
        }

        return null;
    }

    private function fileToDataUri(string $path): string
    {
        $mimeType = mime_content_type($path) ?: 'image/jpeg';

        return 'data:'.$mimeType.';base64,'.base64_encode((string) file_get_contents($path));
    }
}
