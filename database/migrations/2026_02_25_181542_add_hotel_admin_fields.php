<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('apartments', function (Blueprint $table) {
            $table->string('room_class')->nullable()->after('status');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('passport_data')->nullable()->after('role');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('apartments', function (Blueprint $table) {
            $table->dropColumn('room_class');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('passport_data');
        });
    }
};
