<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockOpnameSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'opname_month',
        'opname_year',
        'title',
    ];

    // ✅ Pastikan relasi ke logs sudah benar
    public function logs()
    {
        return $this->hasMany(StockOpnameLog::class, 'stock_opname_session_id');
    }
}