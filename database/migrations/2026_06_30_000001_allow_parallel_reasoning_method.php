<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE rules MODIFY method ENUM('forward','backward','certainty','parallel') NOT NULL DEFAULT 'parallel'");
            DB::statement("ALTER TABLE consultations MODIFY method ENUM('forward','backward','certainty','parallel') NOT NULL DEFAULT 'parallel'");
        }

        DB::table('rules')->whereIn('method', ['forward', 'backward', 'certainty'])->update(['method' => 'parallel']);
    }

    public function down(): void
    {
        DB::table('rules')->where('method', 'parallel')->update(['method' => 'forward']);
        DB::table('consultations')->where('method', 'parallel')->update(['method' => 'forward']);

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE rules MODIFY method ENUM('forward','backward','certainty') NOT NULL DEFAULT 'forward'");
            DB::statement("ALTER TABLE consultations MODIFY method ENUM('forward','backward','certainty') NOT NULL DEFAULT 'forward'");
        }
    }
};
