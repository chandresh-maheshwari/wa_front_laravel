<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quote extends Model
{
    use HasFactory;
    protected $table = 'quote_section';

    protected $fillable = [
        'title',
        'designation',
        'company_name',
        'deleted_at',
        'created_at',
        'updated_at'
    ];
}
