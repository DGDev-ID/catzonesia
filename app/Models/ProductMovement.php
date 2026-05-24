<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

class ProductMovement extends Model
{
    use HasFactory;

    protected $table = 'product_movements';
    protected $fillable = [
        'product_id', 'type', 'opening_stock', 'quantity', 'unit_id', 'closing_stock',
        'transaction_id', 'note', 'price'
    ];

    protected static function booted()
    {
        static::creating(function (ProductMovement $movement) {
            $product = MProduct::find($movement->product_id);
            if (!$product) {
                throw ValidationException::withMessages([
                    'product_id' => 'Produk tidak ditemukan',
                ]);
            }

            $baseUnitId = (int) $product->base_unit_id;
            $requestUnitId = (int) $movement->unit_id;
            $quantity = (float) $movement->quantity;

            if ($quantity <= 0) {
                throw ValidationException::withMessages([
                    'quantity' => 'Quantity harus lebih dari 0',
                ]);
            }

            $quantityConverted = $quantity;

            if ($requestUnitId !== $baseUnitId) {
                $multiplierType = '*';

                $converter = ProductUnitConverter::query()
                    ->where('product_id', $product->id)
                    ->where('unit_from_id', $requestUnitId)
                    ->where('unit_to_id', $baseUnitId)
                    ->first();

                if (!$converter) {
                    $converter = ProductUnitConverter::query()
                        ->where('product_id', $product->id)
                        ->where('unit_from_id', $baseUnitId)
                        ->where('unit_to_id', $requestUnitId)
                        ->first();

                    if ($converter) {
                        $multiplierType = '/';
                    }
                }

                if (!$converter) {
                    throw ValidationException::withMessages([
                        'unit_id' => 'Unit tidak memiliki converter untuk produk ini',
                    ]);
                }

                $multiplier = (float) $converter->multiplier;
                if ($multiplier <= 0) {
                    throw ValidationException::withMessages([
                        'unit_id' => 'Multiplier converter tidak valid',
                    ]);
                }

                $quantityConverted = $multiplierType === '*'
                    ? $quantity * $multiplier
                    : $quantity / $multiplier;
            }

            $openingStock = (float) $product->stock;

            if ($movement->type === 'inbound') {
                $closingStock = $openingStock + $quantityConverted;
            } elseif ($movement->type === 'outbound') {
                $closingStock = $openingStock - $quantityConverted;
            } else {
                throw ValidationException::withMessages([
                    'type' => 'Tipe movement tidak valid',
                ]);
            }

            if ($closingStock < 0) {
                throw ValidationException::withMessages([
                    'quantity' => 'Stok tidak mencukupi',
                ]);
            }

            $movement->opening_stock = $openingStock;
            $movement->closing_stock = $closingStock;
        });

        static::created(function (ProductMovement $movement) {
            $product = MProduct::find($movement->product_id);
            if (!$product) {
                return;
            }

            $product->stock = (int) round((float) $movement->closing_stock);
            $product->save();
        });
    }

    // Relationships
    public function product()
    {
        return $this->belongsTo(MProduct::class, 'product_id');
    }

    public function unit()
    {
        return $this->belongsTo(MUnit::class, 'unit_id');
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'transaction_id');
    }
}
