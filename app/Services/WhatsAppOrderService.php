<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\UserRoleEnum;
use App\Models\Product;
use App\Models\User;
use App\Models\Zone;
use Illuminate\Support\Collection;

class WhatsAppOrderService
{
    /**
     * Resolve the best WhatsApp contact (phone + seller name) for a given zone,
     * falling back to the central WhatsApp configuration.
     *
     * @return array{phone: string, seller_name: ?string, zone_label: ?string}
     */
    public function resolveContactForZone(?int $zoneId): array
    {
        $defaultPhone = $this->sanitizePhone((string) config('project_configuration.whatsapp'));

        if (! $zoneId) {
            return [
                'phone' => $defaultPhone,
                'seller_name' => null,
                'zone_label' => null,
            ];
        }

        $zone = Zone::query()
            ->with([
                'user',
                'users' => fn ($query) => $query
                    ->where('role', UserRoleEnum::SELLER)
                    ->where('status', 'active')
                    ->whereNotNull('phone_number')
                    ->where('phone_number', '!=', ''),
            ])
            ->find($zoneId);

        if (! $zone) {
            return [
                'phone' => $defaultPhone,
                'seller_name' => null,
                'zone_label' => null,
            ];
        }

        $zoneLabel = trim("{$zone->city} — {$zone->name}", ' —');

        /** @var User|null $seller */
        $seller = $zone->users->first();
        if ($seller && filled($seller->phone_number)) {
            return [
                'phone' => $this->sanitizePhone($seller->phone_number),
                'seller_name' => $seller->name,
                'zone_label' => $zoneLabel,
            ];
        }

        if ($zone->user && filled($zone->user->phone_number)) {
            return [
                'phone' => $this->sanitizePhone($zone->user->phone_number),
                'seller_name' => $zone->user->name,
                'zone_label' => $zoneLabel,
            ];
        }

        return [
            'phone' => $defaultPhone,
            'seller_name' => null,
            'zone_label' => $zoneLabel,
        ];
    }

    /**
     * Build a consolidated WhatsApp order URL for multiple cart items.
     *
     * @param  Collection<int, array{product: Product, quantity: int, subtotal: float}>  $items
     */
    public function buildCartWhatsAppUrl(Collection $items, float $total, ?int $zoneId = null): string
    {
        $contact = $this->resolveContactForZone($zoneId);

        $lines = [
            'Bonjour PanoraGoods, je souhaite passer une commande groupée :',
            '',
        ];

        if ($contact['zone_label']) {
            $lines[] = "Zone de livraison : {$contact['zone_label']}";
            if ($contact['seller_name']) {
                $lines[] = "Conseiller assigné : {$contact['seller_name']}";
            }
            $lines[] = '';
        }

        foreach ($items as $index => $item) {
            /** @var Product $product */
            $product = $item['product'];
            $qty = $item['quantity'];
            $subtotal = number_format($item['subtotal'], 0, ',', ' ');
            $unitPrice = number_format((float) $product->selling_price, 0, ',', ' ');
            $ref = $product->reference_number ?: ($product->product_code ?: "#{$product->id}");

            $lines[] = ($index + 1).". {$product->name} (Réf: {$ref})";
            $lines[] = "   Qté : {$qty} × {$unitPrice} FCFA = {$subtotal} FCFA";
            $lines[] = '   Lien : '.route('products.show', $product);
        }

        $lines[] = '';
        $lines[] = 'Total estimé : '.number_format($total, 0, ',', ' ').' FCFA';
        $lines[] = 'Merci de me confirmer la disponibilité et les modalités de livraison.';

        return 'https://wa.me/'.$contact['phone'].'?text='.urlencode(implode("\n", $lines));
    }

    public function sanitizePhone(string $phone): string
    {
        $cleaned = preg_replace('/[^\d]/', '', $phone);

        return $cleaned ?: '237696896758';
    }
}
