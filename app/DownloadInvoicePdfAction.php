<?php

namespace App;

use App\Models\Sale;
use App\Support\Code128Barcode;
use App\Support\TrackingQrCode;
use Barryvdh\DomPDF\Facade\Pdf;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DownloadInvoicePdfAction
{
    public function handle(Sale $sale): StreamedResponse
    {
        $trackingUrl = route('track.order', ['tracking_code' => $sale->tracking_code]);

        $pdf = Pdf::loadView('pdf.invoice', [
            'sale' => $sale,
            'trackingUrl' => $trackingUrl,
            'trackingCode' => $sale->tracking_code,
            'trackingBarcode' => app(Code128Barcode::class)->dataUri($trackingUrl),
            'trackingQrCode' => app(TrackingQrCode::class)->dataUri($trackingUrl),
        ]);
        $pdf->setOption('margin-top', 5);

        return response()->streamDownload(function () use ($pdf): void {
            echo $pdf->stream();
        }, "invoice-{$sale->tracking_code}.pdf");
    }
}
