<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Posts extends Model
{
    use HasFactory;
    protected $table = 'posts';

    protected $fillable = [
        'title',
        'description',
        'image',
        'post_type',
        'ordering',
        'active',
        'deleted_at',
        'created_at',
        'updated_at'
    ];
}
