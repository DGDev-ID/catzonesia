<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('product_unit_converters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('m_products');
            $table->foreignId('unit_from_id')->constrained('m_units')->onDelete('cascade');
            $table->foreignId('unit_to_id')->constrained('m_units')->onDelete('cascade');
            $table->decimal('multiplier', 10, 4);
        });
    }

    public function down()
    {
        Schema::dropIfExists('product_unit_converters');
    }
};
