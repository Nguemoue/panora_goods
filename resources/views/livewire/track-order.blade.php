<div class="max-w-2xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
    <div class="bg-white shadow sm:rounded-lg overflow-hidden">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium leading-6 text-gray-900">{{ __('frontend.track.title') }}</h3>
            <div class="mt-2 max-w-xl text-sm text-gray-500">
                <p>{{ __('frontend.track.subtitle') }}</p>
            </div>
            <form wire:submit.prevent="track" class="mt-5 sm:flex sm:items-center">
                <div class="w-full sm:max-w-xs">
                    <label for="tracking_code" class="sr-only">{{ __('frontend.track.input_label') }}</label>
                    <input type="text" wire:model.defer="tracking_code" id="tracking_code"
                           class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm uppercase"
                           placeholder="ABC123XYZ0">
                </div>
                <button type="submit"
                        class="mt-3 inline-flex w-full items-center justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">

                    <span class="in-data-loading:hidden">{{ __('frontend.track.button') }}</span>
                    <span class="not-in-data-loading:hidden">{{ __('frontend.track.button') }}...</span>

                </button>
            </form>
            @error('tracking_code') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
        </div>

        @if($searched)
            <div class="border-t border-gray-200 px-4 py-5 sm:p-6">
                @if($sale)
                    <div class="space-y-6">
                        <div class="flex items-center justify-between">
                            <h4 class="text-lg font-bold text-gray-900">{{ __('frontend.track.order_title', ['code' => $sale->tracking_code]) }}</h4>
                            <span
                                class="inline-flex items-center rounded-full px-3 py-0.5 text-sm font-medium bg-{{ $sale->status->getColor() }}-100 text-{{ $sale->status->getColor() }}-800">
                                {{ $sale->status->getLabel() }}
                            </span>
                            <a class="btn border bg-blue-500 text-white rounded-sm" href="{{route('invoice.download.pdf',['sale' => $sale])}}">Télécharger la facture</a>
                        </div>

                        <div class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">{{ __('frontend.track.product') }}</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $sale->product->name }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">{{ __('frontend.track.quantity') }}</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $sale->quantity }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">{{ __('frontend.track.customer') }}</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $sale->customer_name }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">{{ __('frontend.track.date') }}</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $sale->created_at->format('M d, Y H:i') }}</dd>
                            </div>
                        </div>

                        <div class="mt-8">
                            <h5 class="text-sm font-medium text-gray-900">{{ __('frontend.track.progress') }}</h5>
                            <div class="mt-4" aria-hidden="true">
                                <div class="overflow-hidden rounded-full bg-gray-200">
                                    @php
                                        $progress = match($sale->status->value) {
                                            'pending' => 20,
                                            'processing' => 40,
                                            'shipped' => 70,
                                            'delivered' => 100,
                                            'cancelled' => 0,
                                            default => 0,
                                        };
                                        $color = $sale->status->value === 'cancelled' ? 'red' : 'indigo';
                                    @endphp
                                    <div class="h-2 rounded-full bg-{{ $color }}-600"
                                         style="width: {{ $progress }}%"></div>
                                </div>
                                <div class="mt-6 hidden grid-cols-4 text-sm font-medium text-gray-600 sm:grid">
                                    <div class="text-indigo-600">{{ __('frontend.track.status.pending') }}</div>
                                    <div
                                        class="text-center {{ $progress >= 40 ? 'text-indigo-600' : '' }}">{{ __('frontend.track.status.processing') }}</div>
                                    <div
                                        class="text-center {{ $progress >= 70 ? 'text-indigo-600' : '' }}">{{ __('frontend.track.status.shipped') }}</div>
                                    <div
                                        class="text-right {{ $progress >= 100 ? 'text-indigo-600' : '' }}">{{ __('frontend.track.status.delivered') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- integrate the payment history --}}
                    <table class="min-w-full divide-y divide-gray-200 mt-8">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Montant</th>
                                <th>État</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($sale->salePayments as $item)
                            <tr>
                                <td class="text-center">{{$loop->index + 1}}</td>
                                <td class="text-center">{{$item->amount}} {{currency()}} </td>
                                <td class="text-center">{{$item->confirmation_status->getLabel()}}</td>
                                <td class="text-center">{{$item->created_at->format('d M, Y H:i')}}</td>
                            </tr>
                        @endforeach

                        </tbody>
                    </table>

                @else
                    <div class="text-center py-6">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24"
                             stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9.172 9.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">{{ __('frontend.track.no_order_found') }}</h3>
                        <p class="mt-1 text-sm text-gray-500">{{ __('frontend.track.no_order_found_text') }}</p>
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>
