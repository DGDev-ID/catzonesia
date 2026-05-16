<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('product_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('m_products');
            $table->foreignId('category_id')->constrained('m_categories');
        });
    }

    public function down()
    {
        Schema::dropIfExists('product_categories');
    }
};