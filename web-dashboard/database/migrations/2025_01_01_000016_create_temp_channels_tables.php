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
        Schema::create('temp_channel_configs', function (Blueprint $table) {
            $table->string('guild_id')->primary();
            $table->string('hub_channel_id'); // ID Channel "Join to Create"
            $table->string('category_id')->nullable(); // Category to spawn channels in
            $table->string('default_name')->default("{user}'s Channel");
            $table->timestamps();
        });

        Schema::create('active_temp_channels', function (Blueprint $table) {
            $table->id();
            $table->string('guild_id')->index();
            $table->string('channel_id')->unique();
            $table->string('owner_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('active_temp_channels');
        Schema::dropIfExists('temp_channel_configs');
    }
};
