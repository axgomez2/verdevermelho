<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('djs', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('social_media')->nullable();
        $table->text('bio');
        $table->string('image')->nullable();
        $table->timestamps();
    });

    // Tabela pivot para recomendações
    Schema::create('dj_vinyl_recommendations', function (Blueprint $table) {
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
        Schema::dropIfExists('d_j_s');
    }
};
