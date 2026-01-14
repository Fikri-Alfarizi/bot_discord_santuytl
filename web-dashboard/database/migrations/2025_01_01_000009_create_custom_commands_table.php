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
        Schema::create('custom_commands', function (Blueprint $table) {
            $table->id();
            $table->string('guild_id')->index();
            $table->string('trigger');
            $table->text('response');
            $table->boolean('is_embed')->default(false);
            $table->json('embed_data')->nullable();
            $table->timestamps();
            
            // Ensure unique trigger per guild
            $table->unique(['guild_id', 'trigger']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('custom_commands');
    }
};
