<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductModel extends Model
{
    use HasFactory;
    // database field
    protected $table = 'product';

    protected $fillable = [
        'title',
        'file',
        'storage',
        'price_per_month_6above',
        'price_per_month_6below',
        'free_insurance',
        'charge',
        'object',
        'time',
        'description',     
        'is_deleted',   
        
        'title_cn',
        'file_cn',
        'storage_cn',
        'price_per_month_6above_cn',
        'price_per_month_6below_cn',
        'free_insurance_cn',
        'charge_cn',
        'object_cn',
        'time_cn',
        'description_cn',  

    ];
}
