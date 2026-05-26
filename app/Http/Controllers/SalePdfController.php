<?php

namespace App\Http\Controllers;

use App\DownloadInvoicePdfAction;
use App\Models\Sale;

class SalePdfController
{
    public function __construct(private DownloadInvoicePdfAction $pdfAction)
    {

    }
    public function __invoke(Sale $sale){
        return $this->pdfAction->handle(sale: $sale);
    }
}
