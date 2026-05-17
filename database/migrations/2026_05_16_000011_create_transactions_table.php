<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('cust_email')->nullable();
            $table->decimal('price', 10, 2);
            $table->decimal('fee', 10, 2);
            $table->decimal('total_price', 10, 2);
            $table->string('status');
            $table->decimal('profit_margin', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('transactions');
    }
};