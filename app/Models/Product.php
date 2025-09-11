<?php

namespace App\Models;

use App\Traits\HasSlug;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, HasSlug, SoftDeletes;

    protected $fillable = [
        'name',
        'image',
        'category_id',
        'supplier_id',
        'description',
        'unit',
        'quantity',
        'code',
        'registered_at',
    ];

    protected function image(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value
                ? asset('storage/products/' . $value)
                : 'https://fakeimg.pl/308x205/?text=Product&font=lexend'
        );
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    // 🔥 Relasi baru: cek apakah produk dipakai di transaksi
    public function transactionDetails()
    {
        return $this->hasMany(\App\Models\TransactionDetail::class, 'product_id');
    }
}