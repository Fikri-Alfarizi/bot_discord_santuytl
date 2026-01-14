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
        Schema::create('social_alerts', function (Blueprint $table) {
            $table->id();
            $table->string('guild_id')->index();
            $table->string('platform'); // twitch, youtube, rss
            $table->string('identifier'); // Channel ID, Username, or URL
            $table->string('discord_channel_id');
            $table->text('message')->nullable();
            $table->string('last_alert_id')->nullable(); // For deduplication (Video ID, Stream ID)
            $table->timestamp('last_check_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('social_alerts');
    }
};
