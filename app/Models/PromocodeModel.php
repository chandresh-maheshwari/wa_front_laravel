<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromocodeModel extends Model
{
    
    use HasFactory;
    // database field
    protected $table = 'promocode';

    protected $fillable = [
        'code',
        'name',
        'period_by',
        'period_to',
        'product_dropdown',          
        'code_cn',
        'name_cn',
        'period_by_cn',
        'period_to_cn',
        'product_dropdown_cn',
        'is_deleted', 
    ];

    protected $table1 = 'products';

    protected $fillable1 = [
        'id',
        'title',
    ];

    public function setCatAttribute($value)
    {
        $this->attributes['product_dropdown_cn'] = json_encode($value);
    }
    // public $timestamps = false;

}
