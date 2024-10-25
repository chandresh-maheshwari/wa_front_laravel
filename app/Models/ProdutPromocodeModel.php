<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProdutPromocodeModel extends Model
{
    use HasFactory;
    protected $table = 'product_promocode';

    protected $fillable = [
        'promocode_id',
        'product_name',
        'price',
        'is_deleted',     

    ];
}
