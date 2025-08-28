<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockOpnameLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'stock_sistem',
        'stock_fisik',
        'selisih',
        'keterangan',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}