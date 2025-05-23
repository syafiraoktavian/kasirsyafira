<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Discount extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'percentage'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
