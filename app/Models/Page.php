<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Page extends Model
{
    use HasFactory;
    protected $table = 'pages';

    protected $fillable = [
        'post_id',
        'title',
        'description',
        'image',
        'ordering',
        'active',
        'deleted_at',
        'created_at',
        'updated_at'
    ];


public function post()
{
    return $this->belongsTo(Posts::class, 'post_id');
}
}