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
        if (!Schema::hasTable('warns')) {
            Schema::create('warns', function (Blueprint $table) {
                $table->id();
                $table->string('guild_id');
                $table->string('user_id');
                $table->string('moderator_id');
                $table->text('reason');
                $table->bigInteger('timestamp'); // UNIX timestamp
                $table->timestamps();
                
                $table->index('guild_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warns');
    }
};
