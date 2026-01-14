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
        Schema::create('automod_rules', function (Blueprint $table) {
            $table->id();
            $table->string('guild_id');
            $table->string('trigger_type'); // bad_word, link, spam
            $table->boolean('enabled')->default(0);
            $table->text('trigger_content')->nullable(); // For comma-separated lists etc.
            $table->string('action')->default('delete'); // delete, timeout, kick, ban
            $table->timestamps(); // Created at and updated at
            
            // Optional: Index on guild_id
            $table->index('guild_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('automod_rules');
    }
};
