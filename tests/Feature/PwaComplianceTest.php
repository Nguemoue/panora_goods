<?php

declare(strict_types=1);

use function Pest\Laravel\get;

it('ships a complete standards-compliant web app manifest and all referenced icons', function () {
    $manifestPath = public_path('manifest.webmanifest');
    expect(file_exists($manifestPath))->toBeTrue();

    $manifest = json_decode((string) file_get_contents($manifestPath), true, 512, JSON_THROW_ON_ERROR);

    expect($manifest['id'])->toBe('/')
        ->and($manifest['name'])->toContain('PanoraGoods')
        ->and($manifest['short_name'])->toBe('PanoraGoods')
        ->and($manifest['display'])->toBe('standalone')
        ->and($manifest['scope'])->toBe('/')
        ->and($manifest['start_url'])->not->toBeEmpty()
        ->and($manifest['theme_color'])->toBe('#004b91')
        ->and($manifest['background_color'])->toBe('#f8fafc')
        ->and($manifest['prefer_related_applications'])->toBeFalse()
        ->and($manifest['launch_handler']['client_mode'])->toBe('navigate-existing')
        ->and($manifest['icons'])->toBeArray()->not->toBeEmpty()
        ->and($manifest['shortcuts'])->toBeArray()->not->toBeEmpty();

    $purposes = collect($manifest['icons'])->pluck('purpose')->unique()->all();
    expect($purposes)->toContain('any')
        ->and($purposes)->toContain('maskable');

    foreach ($manifest['icons'] as $icon) {
        $iconFile = public_path(ltrim((string) $icon['src'], '/'));
        expect(file_exists($iconFile))->toBeTrue("Missing PWA icon: {$icon['src']}");

        if ($icon['type'] === 'image/png' && preg_match('/^(\d+)x(\d+)$/', (string) $icon['sizes'], $matches) === 1) {
            [$width, $height] = getimagesize($iconFile);
            expect($width)->toBe((int) $matches[1])
                ->and($height)->toBe((int) $matches[2]);
        }
    }

    // Verify transparent e-commerce SVG logo has no background rect
    $logoSvg = (string) file_get_contents(public_path('images/logo-mark.svg'));
    expect($logoSvg)->toContain('fill="none"')
        ->and($logoSvg)->not->toContain('<rect');
});

it('ships a service worker that safely bypasses livewire and filament panels', function () {
    $swPath = public_path('sw.js');
    expect(file_exists($swPath))->toBeTrue();

    $sw = (string) file_get_contents($swPath);
    expect($sw)->toContain("'/admin'")
        ->and($sw)->toContain("'/seller'")
        ->and($sw)->toContain("'/client'")
        ->and($sw)->toContain("'/livewire'")
        ->and($sw)->toContain("'/offline'");
});

it('renders the standalone offline fallback page and pwa meta tags on home', function () {
    get(route('offline'))
        ->assertOk()
        ->assertSee('Mode hors ligne')
        ->assertSee('/images/logo-mark.svg');

    get(route('home'))
        ->assertOk()
        ->assertSee('manifest.webmanifest')
        ->assertSee('apple-touch-icon.png')
        ->assertSee('images/logo-mark.svg');
});
