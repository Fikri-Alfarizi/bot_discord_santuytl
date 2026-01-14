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
        Schema::create('birthdays', function (Blueprint $table) {
            $table->id();
            $table->string('guild_id')->index();
            $table->string('user_id')->index(); // Discord User ID
            $table->string('user_name');
            $table->integer('day');
            $table->integer('month');
            $table->integer('last_announced_year')->default(0);
            $table->timestamps();

            $table->unique(['guild_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('birthdays');
    }
};
