<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\ProductImage;
use App\Services\ProductImageOptimizer;
use Illuminate\Console\Command;

class OptimizeProductImages extends Command
{
    protected $signature = 'images:optimize-products {--force : Regenerate existing WebP thumbnails}';

    protected $description = 'Generate optimized WebP thumbnails for all product images';

    public function handle(ProductImageOptimizer $optimizer): int
    {
        $query = ProductImage::query();
        if (! $this->option('force')) {
            $query->whereNull('thumb_path');
        }

        $total = $query->count();
        if ($total === 0) {
            $this->info('Toutes les images produits sont déjà optimisées.');

            return self::SUCCESS;
        }

        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $optimized = 0;
        $query->chunkById(50, function ($images) use ($optimizer, $bar, &$optimized): void {
            foreach ($images as $image) {
                if ($optimizer->generateThumbnail($image) !== null) {
                    $optimized++;
                }
                $bar->advance();
            }
        });

        $bar->finish();
        $this->newLine(2);
        $this->info("{$optimized} miniature(s) WebP générée(s) sur {$total} image(s).");

        return self::SUCCESS;
    }
}
