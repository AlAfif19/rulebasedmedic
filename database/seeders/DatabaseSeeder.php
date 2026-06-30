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

        AppSetting::updateOrCreate(['key' => 'contact_whatsapp'], ['value' => '6281234567890', 'group' => 'contact']);
        AppSetting::updateOrCreate(['key' => 'social_instagram'], ['value' => 'rulebasedmedic', 'group' => 'social']);
        AppSetting::updateOrCreate(['key' => 'social_facebook'], ['value' => 'RuleBasedMedic', 'group' => 'social']);
        AppSetting::updateOrCreate(['key' => 'location'], ['value' => 'Apotek Bhakti Medika Farma, Bandung', 'group' => 'contact']);

        $this->call(MedicalKnowledgeSeeder::class);
    }
}
