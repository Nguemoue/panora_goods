<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>{{ __('panels.catalog_pdf_title') }}</title>
    <style>
        @page {
            margin: 26px;
        }

        body {
            color: #1f2937;
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            line-height: 1.35;
        }

        .header {
            border-bottom: 2px solid #111827;
            margin-bottom: 18px;
            padding-bottom: 12px;
        }

        .header h1 {
            font-size: 24px;
            margin: 0 0 4px;
        }

        .meta {
            color: #6b7280;
            font-size: 9px;
        }

        .category {
            margin-bottom: 22px;
        }

        .category-title {
            background: #111827;
            color: #ffffff;
            font-size: 15px;
            font-weight: bold;
            margin: 0 0 10px;
            padding: 8px 11px;
        }

        .cards-grid {
            border-collapse: separate;
            border-spacing: 8px;
            table-layout: fixed;
            width: 100%;
        }

        .card-cell {
            vertical-align: top;
        }

        .product-card {
            border: 1px solid #d1d5db;
            min-height: 250px;
            padding: 10px;
            page-break-inside: avoid;
        }

        .product-image-wrap {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            height: 96px;
            margin-bottom: 8px;
            padding: 6px;
            text-align: center;
        }

        .product-image {
            max-height: 84px;
            max-width: 100%;
            object-fit: contain;
        }

        .product-name {
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 4px;
        }

        .brand {
            color: #374151;
            font-size: 10px;
            margin-bottom: 4px;
        }

        .model {
            color: #6b7280;
            font-size: 9px;
            margin-bottom: 7px;
        }

        .spec-title {
            border-top: 1px solid #e5e7eb;
            font-size: 10px;
            font-weight: bold;
            margin-top: 7px;
            padding-top: 6px;
        }

        .specifications {
            margin: 4px 0 0;
            padding-left: 12px;
        }

        .specifications li {
            margin-bottom: 2px;
        }

        .empty {
            color: #6b7280;
            font-style: italic;
            padding: 12px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ __('panels.catalog_pdf_title') }}</h1>
        <div class="meta">
            {{ config('project_configuration.system_name') }} -
            {{ __('panels.generated_at') }} {{ $generatedAt->format('d/m/Y H:i') }}
        </div>
    </div>

    @foreach ($categories as $category)
        <section class="category">
            <h2 class="category-title">{{ $category->name }}</h2>

            @if ($category->products->isEmpty())
                <div class="empty">{{ __('panels.no_products_in_category') }}</div>
            @else
                <table class="cards-grid">
                    <tbody>
                        @foreach ($category->products->chunk($columns) as $row)
                            <tr>
                                @foreach ($row as $product)
                                    <td class="card-cell" style="width: {{ 100 / $columns }}%;">
                                        <div class="product-card">
                                            <div class="product-image-wrap">
                                                @if ($product->catalog_image_src)
                                                    <img class="product-image" src="{{ $product->catalog_image_src }}" alt="{{ $product->name }}">
                                                @else
                                                    -
                                                @endif
                                            </div>

                                            <div class="product-name">{{ $product->name }}</div>
                                            <div class="brand">{{ __('phones.brand') }} : {{ $product->brand?->name ?? '-' }}</div>

                                            @if ($product->model_number)
                                                <div class="model">{{ __('panels.model_reference') }} : {{ $product->model_number }}</div>
                                            @endif

                                            <div class="spec-title">{{ __('panels.technical_specifications') }}</div>

                                            @if ($product->specifications->isEmpty())
                                                <div class="meta">-</div>
                                            @else
                                                <ul class="specifications">
                                                    @foreach ($product->specifications as $specification)
                                                        <li>
                                                            <strong>{{ $specification->name }} :</strong>
                                                            {{ $specification->pivot->value }}
                                                            {{ $specification->measure }}
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @endif
                                        </div>
                                    </td>
                                @endforeach

                                @for ($emptyCells = $row->count(); $emptyCells < $columns; $emptyCells++)
                                    <td class="card-cell" style="width: {{ 100 / $columns }}%;"></td>
                                @endfor
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </section>
    @endforeach
</body>
</html>
