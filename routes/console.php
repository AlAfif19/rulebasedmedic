<?php

use Illuminate\Support\Facades\Artisan;

Artisan::command('rulebasedmedic:about', function () {
    $this->info('RuleBasedMedic siap digunakan. Jalankan start.sh untuk migrasi, seed, dan server lokal.');
})->purpose('Menampilkan informasi singkat project RuleBasedMedic');
