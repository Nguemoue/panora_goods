<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>{{ __('panels.catalog_pdf_title') }}</title>
    <style>
        @page {
            margin: 28px;
        }

        body {
            color: #1f2937;
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            line-height: 1.35;
        }

        .header {
            border-bottom: 2px solid #111827;
            margin-bottom: 20px;
            padding-bottom: 12px;
        }

        .header h1 {
            font-size: 24px;
            margin: 0 0 4px;
        }

        .meta {
            color: #6b7280;
            font-size: 10px;
        }

        .category {
            page-break-inside: avoid;
            margin-bottom: 22px;
        }

        .category-title {
            background: #111827;
            color: #ffffff;
            font-size: 16px;
            font-weight: bold;
            margin: 0 0 10px;
            padding: 9px 12px;
        }

        table.products {
            border-collapse: collapse;
            width: 100%;
        }

        table.products th {
            background: #f3f4f6;
            border: 1px solid #d1d5db;
            font-size: 11px;
            padding: 7px;
            text-align: left;
        }

        table.products td {
            border: 1px solid #d1d5db;
            padding: 7px;
            vertical-align: top;
        }

        .image-cell {
            text-align: center;
            width: 92px;
        }

        .product-image {
            height: 78px;
            max-width: 78px;
            object-fit: contain;
        }

        .product-name {
            font-size: 13px;
            font-weight: bold;
            margin-bottom: 4px;
        }

        .brand {
            color: #374151;
        }

        .specifications {
            margin: 0;
            padding-left: 14px;
        }

        .specifications li {
            margin-bottom: 3px;
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
                <table class="products">
                    <thead>
                        <tr>
                            <th>{{ __('phones.image') }}</th>
                            <th>{{ __('panels.product_name') }}</th>
                            <th>{{ __('phones.brand') }}</th>
                            <th>{{ __('panels.technical_specifications') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($category->products as $product)
                            <tr>
                                <td class="image-cell">
                                    @if ($product->catalog_image_src)
                                        <img class="product-image" src="{{ $product->catalog_image_src }}" alt="{{ $product->name }}">
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    <div class="product-name">{{ $product->name }}</div>
                                    @if ($product->model_number)
                                        <div class="meta">{{ __('panels.model_reference') }} : {{ $product->model_number }}</div>
                                    @endif
                                </td>
                                <td class="brand">{{ $product->brand?->name ?? '-' }}</td>
                                <td>
                                    @if ($product->specifications->isEmpty())
                                        -
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
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </section>
    @endforeach
</body>
</html>
