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
        Schema::table('guild_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('guild_settings', 'pro_expires_at')) {
                $table->timestamp('pro_expires_at')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('guild_settings', function (Blueprint $table) {
            $table->dropColumn(['pro_expires_at']);
        });
    }
};
