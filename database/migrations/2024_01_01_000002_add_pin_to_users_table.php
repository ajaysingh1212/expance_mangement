<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add PIN authentication columns to users table.
     *
     * pin          — bcrypt-hashed 4-digit PIN (nullable = PIN not set yet)
     * pin_enabled  — user can disable PIN lock even if they have one set
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('pin', 255)->nullable()->after('password');
            $table->boolean('pin_enabled')->default(false)->after('pin');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['pin', 'pin_enabled']);
        });
    }
};
