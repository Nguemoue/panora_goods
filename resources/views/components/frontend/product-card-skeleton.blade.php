{{-- Exact-geometry Skeleton Card matching product-card.blade.php (CLS = 0, Light & Dark) --}}
<div class="flex flex-col overflow-hidden rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs" aria-hidden="true">
    <flux:skeleton.group animate="shimmer">
        <div class="aspect-square w-full bg-slate-50/80 dark:bg-slate-800/60 p-3 border-b border-slate-100 dark:border-slate-800 flex items-center justify-center">
            <flux:skeleton class="size-full rounded-xl" />
        </div>

        <div class="flex flex-1 flex-col p-3 sm:p-4 space-y-3">
            <div class="flex items-center justify-between gap-2">
                <flux:skeleton class="h-3 w-20" />
                <flux:skeleton class="h-3 w-12" />
            </div>

            <div class="space-y-1.5 min-h-[2.25rem] sm:min-h-[2.5rem]">
                <flux:skeleton class="h-4 w-5/6" />
                <flux:skeleton class="h-4 w-2/3" />
            </div>

            <div class="flex gap-1.5">
                <flux:skeleton class="h-5 w-12 rounded-md" />
                <flux:skeleton class="h-5 w-14 rounded-md" />
            </div>

            <div class="pt-2 space-y-2.5">
                <flux:skeleton class="h-6 w-28" />
                <flux:skeleton class="h-8 w-full rounded-lg" />
            </div>
        </div>
    </flux:skeleton.group>
</div>
