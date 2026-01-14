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
            if (!Schema::hasColumn('guild_settings', 'birthday_channel_id')) {
                $table->string('birthday_channel_id')->nullable();
                $table->string('birthday_message')->nullable()->default('Happy Birthday {user}! 🎉');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('guild_settings', function (Blueprint $table) {
            $table->dropColumn(['birthday_channel_id', 'birthday_message']);
        });
    }
};
