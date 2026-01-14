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
        Schema::create('stats_channels', function (Blueprint $table) {
            $table->id();
            $table->string('guild_id')->index();
            $table->string('channel_id')->unique(); // The Voice Channel ID on Discord
            $table->string('type'); // members, bots, roles, online
            $table->string('data')->nullable(); // Extended data (e.g. role_id for type=roles)
            $table->string('format')->default('Members: {count}');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stats_channels');
    }
};
