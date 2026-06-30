<?php

namespace App\Http\Controllers;

use App\Models\Disease;
use App\Models\Medicine;
use App\Models\Symptom;
use App\Models\Rule;
use App\Models\AppSetting;

class HomeController extends Controller
{
    public function landing()
    {
        return view('landing', [
            'symptomCount' => Symptom::count(),
            'diseaseCount' => Disease::count(),
            'medicineCount' => Medicine::count(),
            'ruleCount' => Rule::count(),
        ]);
    }

    public function dashboard()
    {
        return view('user.dashboard', [
            'featuredDiseases' => Disease::query()->where('is_active', true)->take(6)->get(),
            'medicineCount' => Medicine::count(),
            'symptomCount' => Symptom::count(),
        ]);
    }

    public function information()
    {
        $query = Medicine::query()->where('is_active', true)->orderBy('name');

        if ($search = request()->string('q')->toString()) {
            $query->where(function ($inner) use ($search) {
                $inner->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        return view('information', [
            'medicines' => $query->paginate(12)->withQueryString(),
            'contact' => $this->contactDetails(),
        ]);
    }

    private function contactDetails(): array
    {
        $settings = AppSetting::query()->pluck('value', 'key');

        return [
            'pharmacy_name' => $settings->get('pharmacy_name', 'Apotek Bhakti Medika Farma'),
            'phone' => $settings->get('contact_phone_display', '+62 822-4674-0801'),
            'whatsapp' => $settings->get('contact_whatsapp', '6282246740801'),
            'instagram' => $settings->get('social_instagram', 'bhaktimedikafarma'),
            'facebook' => $settings->get('social_facebook', 'Apotek Bhakti Medika Farma'),
            'address' => $settings->get('location', 'Jl. Moch. Toha No.77, Cigereleng, Kec. Regol, Kota Bandung, Jawa Barat 40253, Indonesia'),
            'hours' => $settings->get('opening_hours', 'Senin - Sabtu, 08.00 - 20.00. Minggu tutup.'),
            'hours_short' => 'Senin - Sabtu, 08.00 - 20.00',
            'maps_plus_code' => $settings->get('maps_plus_code', '3J64+VX Cigereleng, Bandung City, West Java, Indonesia'),
            'maps_url' => $settings->get('maps_url', 'https://maps.app.goo.gl/3Jw47coZGatRMsci9'),
            'osm_embed_url' => $settings->get('osm_embed_url', 'https://www.openstreetmap.org/export/embed.html?bbox=107.6025%2C-6.9368%2C107.6125%2C-6.9268&layer=mapnik&marker=-6.9318%2C107.6075'),
        ];
    }
}
