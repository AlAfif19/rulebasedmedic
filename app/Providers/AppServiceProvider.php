<?php

namespace App\Providers;

use App\Models\AppSetting;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Register app services here.
    }

    public function boot(): void
    {
        View::composer('partials.footer', function ($view) {
            $settings = Schema::hasTable('app_settings')
                ? AppSetting::query()->pluck('value', 'key')
                : collect();

            $view->with('contact', [
                'pharmacy_name' => $settings->get('pharmacy_name', 'Apotek Bhakti Medika Farma'),
                'phone' => $settings->get('contact_phone_display', '+62 822-4674-0801'),
                'whatsapp' => $settings->get('contact_whatsapp', '6282246740801'),
                'instagram' => $settings->get('social_instagram', 'bhaktimedikafarma'),
                'facebook' => $settings->get('social_facebook', 'Apotek Bhakti Medika Farma'),
                'address' => $settings->get('location', 'Jl. Moch. Toha No.77, Cigereleng, Kec. Regol, Kota Bandung, Jawa Barat 40253, Indonesia'),
                'hours' => $settings->get('opening_hours', 'Senin - Sabtu, 08.00 - 20.00. Minggu tutup.'),
                'maps_plus_code' => $settings->get('maps_plus_code', '3J64+VX Cigereleng, Bandung City, West Java, Indonesia'),
                'maps_url' => $settings->get('maps_url', 'https://maps.app.goo.gl/3Jw47coZGatRMsci9'),
                'osm_embed_url' => $settings->get('osm_embed_url', 'https://www.openstreetmap.org/export/embed.html?bbox=107.6025%2C-6.9368%2C107.6125%2C-6.9268&layer=mapnik&marker=-6.9318%2C107.6075'),
            ]);
        });
    }
}
