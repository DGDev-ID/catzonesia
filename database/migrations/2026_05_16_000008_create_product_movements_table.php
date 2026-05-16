<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('product_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('m_products');
            $table->enum('type', ['inbound', 'outbound', 'adjustment']);
            $table->integer('quantity');
            $table->foreignId('unit_id')->constrained('m_units');
            $table->foreignId('transaction_id')->nullable()->constrained('transactions');
            $table->text('note')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('product_movements');
    }
};