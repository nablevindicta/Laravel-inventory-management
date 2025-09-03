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
        'stock_opname_session_id', 
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function session()
    {
        return $this->belongsTo(StockOpnameSession::class, 'stock_opname_session_id');
    }
}