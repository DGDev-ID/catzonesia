<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('package_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('package_id')->constrained('m_package');
            $table->foreignId('product_id')->constrained('m_products');
        });
    }

    public function down()
    {
        Schema::dropIfExists('package_products');
    }
};