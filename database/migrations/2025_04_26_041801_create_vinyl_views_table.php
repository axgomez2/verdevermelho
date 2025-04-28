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
        Schema::create('vinyl_views', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vinyl_master_id')->constrained('vinyl_masters')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->string('ip_address', 45)->nullable();
            $table->timestamp('viewed_at');
            $table->timestamps();
            
            // Índices para melhorar a performance das consultas
            $table->index(['vinyl_master_id', 'viewed_at']);
            $table->index(['user_id', 'vinyl_master_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vinyl_views');
    }
};
