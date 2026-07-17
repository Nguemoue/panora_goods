<?php

use App\DownloadInvoicePdfAction;
use App\Livewire\TrackOrder;
use App\Models\Sale;
use App\Support\Code128Barcode;
use App\Support\TrackingQrCode;
use Barryvdh\DomPDF\Facade\Pdf;
use Livewire\Livewire;

it('generates a code 128 barcode data uri for a tracking url', function () {
    $trackingUrl = route('track.order', ['tracking_code' => 'ABC1234567']);

    $barcode = app(Code128Barcode::class)->dataUri($trackingUrl);

    expect($barcode)
        ->toStartWith('data:image/svg+xml;base64,')
        ->and(base64_decode(str($barcode)->after('base64,')->toString()))->toContain('<svg');
});

it('generates a qr code data uri for a tracking url', function () {
    $trackingUrl = route('track.order', ['tracking_code' => 'ABC1234567']);

    $qrCode = app(TrackingQrCode::class)->dataUri($trackingUrl);

    expect($qrCode)
        ->toStartWith('data:image/svg+xml;base64,')
        ->and(base64_decode(str($qrCode)->after('base64,')->toString()))->toContain('<svg');
});

it('renders the invoice pdf with a tracking barcode and qr code', function () {
    $sale = Sale::factory()->create(['tracking_code' => 'ABC1234567']);
    $trackingUrl = route('track.order', ['tracking_code' => $sale->tracking_code]);
    $trackingBarcode = app(Code128Barcode::class)->dataUri($trackingUrl);
    $trackingQrCode = app(TrackingQrCode::class)->dataUri($trackingUrl);

    $html = view('pdf.invoice', [
        'sale' => $sale,
        'trackingUrl' => $trackingUrl,
        'trackingCode' => $sale->tracking_code,
        'trackingBarcode' => $trackingBarcode,
        'trackingQrCode' => $trackingQrCode,
    ])->render();

    expect($html)
        ->toContain('Code-barres de suivi')
        ->toContain('QR code de suivi')
        ->toContain('Scannez ce QR code')
        ->toContain('ABC1234567')
        ->toContain(e($trackingUrl));

    $pdf = Pdf::loadHTML($html);

    expect($pdf->output())->toStartWith('%PDF');
});

it('downloads an invoice pdf with the tracking barcode data', function () {
    $sale = Sale::factory()->create(['tracking_code' => 'ABC1234567']);

    $response = app(DownloadInvoicePdfAction::class)->handle($sale);

    expect($response->headers->get('content-disposition'))->toContain('invoice-ABC1234567.pdf');
});

it('prefills the tracking input from the tracking code query string', function () {
    Livewire::withQueryParams(['tracking_code' => 'ABC1234567'])
        ->test(TrackOrder::class)
        ->assertSet('tracking_code', 'ABC1234567');
});
