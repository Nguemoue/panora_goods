<?php
declare(strict_types=1);

namespace App\Services;

/**
 * This class will generate a product code based on the name
 */
class ProductCodeGenerator
{
    /**
     * Generate the product code base on the name and make sure it is unique
     * @param string $productName
     * @return string
     */
    public function generate(string $productName): string
    {
        return str($productName)
            ->substr(0,2)
            ->append(date('dmyhis'))->upper()->toString();
    }
}
