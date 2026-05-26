<?php

namespace App;

use App\Models\Sale;
use Barryvdh\DomPDF\Facade\Pdf;

class DownloadInvoicePdfAction
{
    public function handle(Sale $sale): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $pdf = Pdf::loadView('pdf.invoice', ['sale' => $sale]);

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, "invoice-{$sale->tracking_code}.pdf");
    }
}
