@props(['product'])

<div class="card h-full shadow-sm hover:shadow-md transition-shadow">
    <!-- Image -->
    <a href="{{ route('products.show', $product) }}" class="block p-4 border-b border-gray-100 aspect-square flex items-center justify-center">
        @php
            $primaryImage = $product->images->where('is_primary', true)->first() ?? $product->images->first();
        @endphp
        <img src="{{ $primaryImage ? asset('storage/' . $primaryImage->path) : 'https://placehold.co/400x400?text=' . urlencode($product->name) }}" 
             alt="{{ $product->name }}" 
             class="max-w-full max-h-full object-contain">
    </a>

    <!-- Card Body -->
    <div class="flex flex-col flex-grow p-4">
        <p class="text-[10px] text-primary font-bold uppercase mb-1">{{ $product->brand->name ?? 'Marque' }}</p>
        <h5 class="text-sm font-bold text-gray-800 mb-3 line-clamp-2 min-h-[40px]">
            <a href="{{ route('products.show', $product) }}" class="hover:text-primary transition-colors">
                {{ $product->name }}
            </a>
        </h5>
        
        <div class="mt-auto">
            <div class="mb-3">
                <span class="text-lg font-black text-secondary">{{ Number::currency($product->selling_price, 'XAF') }}</span>
            </div>
            
            <a href="{{ route('products.show', $product) }}" class="btn btn-primary w-full text-xs font-bold uppercase">
                Voir le détail
            </a>
        </div>
    </div>
</div>
