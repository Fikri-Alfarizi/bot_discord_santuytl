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
        if (!Schema::hasTable('welcome_infos')) {
            Schema::create('welcome_infos', function (Blueprint $table) {
                $table->id();
                $table->string('guild_id')->index();
                $table->string('channel_id');
                $table->text('message_content')->nullable();
                $table->json('embed_data')->nullable(); // Stores title, description, color, image, etc.
                $table->timestamps();

                $table->unique('guild_id'); // One welcome info per guild for now
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('welcome_infos');
    }
};
