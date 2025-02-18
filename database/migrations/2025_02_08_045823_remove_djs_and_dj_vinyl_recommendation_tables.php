<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveDeejayVinylChartsAndDeejayTablesFinal extends Migration
{
    public function up()
    {
        // Primeiro, remova a tabela deejay_vinyl_charts
        Schema::dropIfExists('deejay_vinyl_chart');

        // Em seguida, remova a tabela deejays
        Schema::dropIfExists('deejays');
    }

    public function down()
    {
        // Recrie a tabela deejays
        Schema::create('deejays', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('social_media')->nullable();
            $table->text('bio');
            $table->string('image')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        // Recrie a tabela deejay_vinyl_chart
        Schema::create('deejay_vinyl_chart', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dj_id')->constrained('deejays')->onDelete('cascade');
            $table->foreignId('vinyl_master_id')->constrained()->onDelete('cascade');
            $table->integer('order')->unsigned();
            $table->timestamps();
        });
    }
}