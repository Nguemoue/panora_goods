<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice - {{ $sale->tracking_code }}</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #333;
        }

        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 30px;
            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, .15);
            font-size: 16px;
            line-height: 24px;
            color: #555;
        }

        .invoice-box table {
            width: 100%;
            line-height: inherit;
            text-align: left;
        }

        .invoice-box table td {
            padding: 5px;
            vertical-align: top;
        }

        .invoice-box table tr td:nth-child(2) {
            text-align: right;
        }

        .invoice-box table tr.top table td {
            padding-bottom: 20px;
        }

        .invoice-box table tr.top table td.title {
            font-size: 40px;
            line-height: 45px;
            color: #333;
        }

        .invoice-box table tr.information table td {
            padding-bottom: 40px;
        }

        .invoice-box table tr.heading td {
            background: #eee;
            border-bottom: 1px solid #ddd;
            font-weight: bold;
        }

        .invoice-box table tr.details td {
            padding-bottom: 20px;
        }

        .invoice-box table tr.item td {
            border-bottom: 1px solid #eee;
        }

        .invoice-box table tr.item.last td {
            border-bottom: none;
        }

        .invoice-box table tr.total td:nth-child(2) {
            border-top: 2px solid #eee;
            font-weight: bold;
        }

        .rtl {
            direction: rtl;
            font-family: Tahoma, 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
        }

        .rtl table {
            text-align: right;
        }

        .rtl table tr td:nth-child(2) {
            text-align: left;
        }
    </style>
</head>
<body>
<div class="invoice-box">
    <table cellpadding="0" cellspacing="0">
        <tr class="top">
            <td colspan="2">
                <table>
                    <tr>
                        <td class="title">
                            <h2>{{__('Invoice')}}</h2>
                            <p style="font-size: 14px; margin-top: 5px;">
                                {{config('project_configuration.system_name')}} ( Une marque de SOFITRAPAM SARL )
                            </p>
                        </td>
                        <td>
                            Facture #: {{ $sale->id }}<br>
                            Code de suivi: <b>{{ $sale->tracking_code }}</b><br>
                            Généré le: {{ $sale->created_at->format('M d, Y') }}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr class="information">
            <td colspan="2">
                <table>
                    <tr>
                        <td>
                            Vendeur: {{ $sale->seller->name }}<br>
                            {{ $sale->seller->email }}
                        </td>
                        <td>
                            Client: {{ $sale->customer_name }}<br>
                            Statut: {{ $sale->status->getLabel() }}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr class="heading">
            <td>Produit</td>
            <td>Prix</td>
        </tr>
        <tr class="item">
            <td>{{ $sale->product->name }} (x{{ $sale->quantity }})</td>
            <td>{{ number_format($sale->sale_price, 2) }}</td>
        </tr>
        <tr class="total">
            <td></td>
            <td>Total: {{ number_format($sale->sale_price * $sale->quantity, 2) }}</td>
        </tr>
    </table>
</div>
<div class="" style="text-align: end; margin-top: 20px;">
    SOFITRAPAM SARL | NIU : M0126183474775 | RCCM : CM-BFX-01-2026-B13-00009
</div>

</body>
</html>
