<?php

namespace App\Models;

use App\Traits\HasSlug;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Product extends Model
{
    use HasFactory, HasSlug;

    // ✅ Lebih aman daripada $guarded = []
    protected $fillable = [
        'name',
        'image',
        'category_id',
        'supplier_id',
        'description',
        'unit',
        'quantity',
        'code',
    ];

    // ✅ Modern attribute casting
    protected function image(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value
                ? asset('storage/products/' . $value)
                : 'https://fakeimg.pl/308x205/?text=Product&font=lexend'
        );
    }

    // ✅ Relasi
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

}