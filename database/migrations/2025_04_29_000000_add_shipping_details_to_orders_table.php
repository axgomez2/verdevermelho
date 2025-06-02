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
        Schema::table('orders', function (Blueprint $table) {
            $table->string('shipping_service_id')->nullable()->after('shipping_cost');
            $table->string('shipping_service_name')->nullable()->after('shipping_service_id');
            $table->integer('shipping_delivery_time')->nullable()->after('shipping_service_name');
            $table->string('shipping_tracking_code')->nullable()->after('shipping_delivery_time');
            $table->json('shipping_label_info')->nullable()->after('shipping_tracking_code');
            $table->timestamp('shipping_date')->nullable()->after('shipping_label_info');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'shipping_service_id',
                'shipping_service_name',
                'shipping_delivery_time',
                'shipping_tracking_code',
                'shipping_label_info',
                'shipping_date',
            ]);
        });
    }
};
