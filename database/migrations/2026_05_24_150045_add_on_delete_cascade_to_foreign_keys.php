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
        // m_products table
        Schema::table('m_products', function (Blueprint $table) {
            $table->dropForeign(['base_unit_id']);
            $table->foreign('base_unit_id')->references('id')->on('m_units')->onDelete('cascade');
        });

        // product_categories table
        Schema::table('product_categories', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->dropForeign(['category_id']);
            $table->foreign('product_id')->references('id')->on('m_products')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('m_categories')->onDelete('cascade');
        });

        // product_unit_converters table
        Schema::table('product_unit_converters', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->dropForeign(['unit_id']);
            $table->foreign('product_id')->references('id')->on('m_products')->onDelete('cascade');
            $table->foreign('unit_id')->references('id')->on('m_units')->onDelete('cascade');
        });

        // product_movements table
        Schema::table('product_movements', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->dropForeign(['unit_id']);
            $table->dropForeign(['transaction_id']);
            $table->foreign('product_id')->references('id')->on('m_products')->onDelete('cascade');
            $table->foreign('unit_id')->references('id')->on('m_units')->onDelete('cascade');
            $table->foreign('transaction_id')->references('id')->on('transactions')->nullOnDelete()->onDelete('cascade');
        });

        // package_products table
        Schema::table('package_products', function (Blueprint $table) {
            $table->dropForeign(['package_id']);
            $table->dropForeign(['product_id']);
            $table->foreign('package_id')->references('id')->on('m_package')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('m_products')->onDelete('cascade');
        });

        // transaction_details table
        Schema::table('transaction_details', function (Blueprint $table) {
            $table->dropForeign(['transaction_id']);
            $table->dropForeign(['package_id']);
            $table->dropForeign(['product_id']);
            $table->dropForeign(['unit_id']);
            $table->foreign('transaction_id')->references('id')->on('transactions')->onDelete('cascade');
            $table->foreign('package_id')->references('id')->on('m_package')->nullOnDelete()->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('m_products')->nullOnDelete()->onDelete('cascade');
            $table->foreign('unit_id')->references('id')->on('m_units')->onDelete('cascade');
        });

        // transaction_groomings table
        Schema::table('transaction_groomings', function (Blueprint $table) {
            $table->dropForeign(['transaction_id']);
            $table->foreign('transaction_id')->references('id')->on('transactions')->onDelete('cascade');
        });

        // transaction_logs table
        Schema::table('transaction_logs', function (Blueprint $table) {
            $table->dropForeign(['transaction_id']);
            $table->foreign('transaction_id')->references('id')->on('transactions')->onDelete('cascade');
        });

        // sessions table
        Schema::table('sessions', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // sessions table
        Schema::table('sessions', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->foreign('user_id')->references('id')->on('users');
        });

        // transaction_logs table
        Schema::table('transaction_logs', function (Blueprint $table) {
            $table->dropForeign(['transaction_id']);
            $table->foreign('transaction_id')->references('id')->on('transactions');
        });

        // transaction_groomings table
        Schema::table('transaction_groomings', function (Blueprint $table) {
            $table->dropForeign(['transaction_id']);
            $table->foreign('transaction_id')->references('id')->on('transactions');
        });

        // transaction_details table
        Schema::table('transaction_details', function (Blueprint $table) {
            $table->dropForeign(['transaction_id']);
            $table->dropForeign(['package_id']);
            $table->dropForeign(['product_id']);
            $table->dropForeign(['unit_id']);
            $table->foreign('transaction_id')->references('id')->on('transactions');
            $table->foreign('package_id')->references('id')->on('m_package')->nullOnDelete();
            $table->foreign('product_id')->references('id')->on('m_products')->nullOnDelete();
            $table->foreign('unit_id')->references('id')->on('m_units');
        });

        // package_products table
        Schema::table('package_products', function (Blueprint $table) {
            $table->dropForeign(['package_id']);
            $table->dropForeign(['product_id']);
            $table->foreign('package_id')->references('id')->on('m_package');
            $table->foreign('product_id')->references('id')->on('m_products');
        });

        // product_movements table
        Schema::table('product_movements', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->dropForeign(['unit_id']);
            $table->dropForeign(['transaction_id']);
            $table->foreign('product_id')->references('id')->on('m_products');
            $table->foreign('unit_id')->references('id')->on('m_units');
            $table->foreign('transaction_id')->references('id')->on('transactions')->nullOnDelete();
        });

        // product_unit_converters table
        Schema::table('product_unit_converters', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->dropForeign(['unit_id']);
            $table->foreign('product_id')->references('id')->on('m_products');
            $table->foreign('unit_id')->references('id')->on('m_units');
        });

        // product_categories table
        Schema::table('product_categories', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->dropForeign(['category_id']);
            $table->foreign('product_id')->references('id')->on('m_products');
            $table->foreign('category_id')->references('id')->on('m_categories');
        });

        // m_products table
        Schema::table('m_products', function (Blueprint $table) {
            $table->dropForeign(['base_unit_id']);
            $table->foreign('base_unit_id')->references('id')->on('m_units');
        });
    }
};