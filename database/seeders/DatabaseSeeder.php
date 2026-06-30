<?php

namespace Database\Seeders;

use App\Models\AppSetting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@rulebasedmedic.local'],
            [
                'name' => 'Admin Apoteker',
                'username' => 'admin',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'phone' => '081234567890',
                'address' => 'Apotek Bhakti Medika Farma',
            ]
        );

        User::updateOrCreate(
            ['email' => 'masyarakat@rulebasedmedic.local'],
            [
                'name' => 'User Masyarakat',
                'username' => 'masyarakat',
                'password' => Hash::make('password'),
                'role' => 'masyarakat',
                'phone' => '081111111111',
                'address' => 'Bandung',
            ]
        );

        AppSetting::updateOrCreate(['key' => 'pharmacy_name'], ['value' => 'Apotek Bhakti Medika Farma', 'group' => 'contact']);
        AppSetting::updateOrCreate(['key' => 'contact_whatsapp'], ['value' => '6282246740801', 'group' => 'contact']);
        AppSetting::updateOrCreate(['key' => 'contact_phone_display'], ['value' => '+62 822-4674-0801', 'group' => 'contact']);
        AppSetting::updateOrCreate(['key' => 'social_instagram'], ['value' => 'bhaktimedikafarma', 'group' => 'social']);
        AppSetting::updateOrCreate(['key' => 'social_facebook'], ['value' => 'Apotek Bhakti Medika Farma', 'group' => 'social']);
        AppSetting::updateOrCreate(['key' => 'location'], ['value' => 'Jl. Moch. Toha No.77, Cigereleng, Kec. Regol, Kota Bandung, Jawa Barat 40253, Indonesia', 'group' => 'contact']);
        AppSetting::updateOrCreate(['key' => 'opening_hours'], ['value' => 'Senin - Sabtu, 08.00 - 20.00. Minggu tutup.', 'group' => 'contact']);
        AppSetting::updateOrCreate(['key' => 'maps_plus_code'], ['value' => '3J64+VX Cigereleng, Bandung City, West Java, Indonesia', 'group' => 'contact']);
        AppSetting::updateOrCreate(['key' => 'maps_url'], ['value' => 'https://maps.app.goo.gl/3Jw47coZGatRMsci9', 'group' => 'contact']);

        $this->call(MedicalKnowledgeSeeder::class);
    }
}
