<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderModel extends Model
{
    protected $table = 'cart_table';

    protected $fillable = [
        'productid',
        'userid',
        'order_id',
        'product_name',
        'quantity',
        'price',
        'total',
        'subtotal',
        'created_at',
        'updated_at',
    ];
}
