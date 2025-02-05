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
        Schema::create('deejay_vinyl_chart', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dj_id')->constrained()->onDelete('cascade');
            $table->foreignId('vinyl_master_id')->constrained()->onDelete('cascade');
            $table->integer('order')->unsigned();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deejay_vinyl_chart');
    }
};
