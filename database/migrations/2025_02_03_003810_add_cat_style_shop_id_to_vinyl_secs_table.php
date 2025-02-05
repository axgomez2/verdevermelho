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
        Schema::table('vinyl_secs', function (Blueprint $table) {
            $table->unsignedBigInteger('cat_style_shop_id')->nullable();
            $table->foreign('cat_style_shop_id')->references('id')->on('cat_style_shop')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('vinyl_secs', function (Blueprint $table) {
            $table->dropForeign(['cat_style_shop_id']);
            $table->dropColumn('cat_style_shop_id');
        });
    }
};
