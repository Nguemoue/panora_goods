{{-- Exact-geometry Skeleton Card matching product-card.blade.php (CLS = 0) --}}
<div class="flex flex-col overflow-hidden rounded-2xl bg-white border border-slate-200/80 shadow-xs" aria-hidden="true">
    <flux:skeleton.group animate="shimmer">
        {{-- Square Image Skeleton --}}
        <div class="aspect-square w-full bg-slate-50/80 p-4 border-b border-slate-100 flex items-center justify-center">
            <flux:skeleton class="size-full rounded-xl" />
        </div>

        {{-- Body Skeleton --}}
        <div class="flex flex-1 flex-col p-4 space-y-3">
            <div class="flex items-center justify-between gap-2">
                <flux:skeleton class="h-3 w-20" />
                <flux:skeleton class="h-3 w-14" />
            </div>

            <div class="space-y-1.5 min-h-[2.5rem]">
                <flux:skeleton class="h-4 w-5/6" />
                <flux:skeleton class="h-4 w-2/3" />
            </div>

            <div class="flex gap-1.5">
                <flux:skeleton class="h-5 w-14 rounded-md" />
                <flux:skeleton class="h-5 w-16 rounded-md" />
                <flux:skeleton class="h-5 w-12 rounded-md" />
            </div>

            <div class="pt-2 space-y-3">
                <flux:skeleton class="h-6 w-32" />
                <div class="flex items-center gap-2">
                    <flux:skeleton class="h-8 flex-1 rounded-lg" />
                    <flux:skeleton class="size-8 rounded-lg" />
                    <flux:skeleton class="size-8 rounded-lg" />
                </div>
            </div>
        </div>
    </flux:skeleton.group>
</div>
