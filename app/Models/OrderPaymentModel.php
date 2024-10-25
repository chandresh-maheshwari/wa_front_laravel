<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderPaymentModel extends Model
{
    use HasFactory;

    protected $table = 'order_payment';

    protected $fillable = [
        'cart_id',
        'university_student',
        'instructions',
        'walk_up',
        'health_protection',
        'payment_method',
        'card_number', 
        'expiree_date',
        'csv',
        'status',
        'is_deleted',
        ];
}
