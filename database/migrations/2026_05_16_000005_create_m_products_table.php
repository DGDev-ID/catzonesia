<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('m_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('base_unit_id')->constrained('m_units');
            $table->string('name');
            $table->string('sku');
            $table->decimal('price', 10, 2);
            $table->decimal('avg_inbound_price', 10, 2);
            $table->integer('stock');
            $table->integer('stock_alert');
            $table->text('description')->nullable();
            $table->text('img_url')->nullable();
            $table->boolean('is_display')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('m_products');
    }
};