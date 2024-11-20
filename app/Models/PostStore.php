<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostStore extends Model
{
    use HasFactory;

    protected $table = 'post_store';

    protected $fillable = [
        'post_name',
        'data',
        'status',
        'deleted_at',
        'created_at',
        'updated_at'
    ];
        protected $casts = [
        'data' => 'array',
    ];

        public function __get($key)
    {
        if ($key === 'data' && isset($this->attributes['data'])) {
            return json_decode($this->attributes['data'], true);
        }

        if (array_key_exists($key, $this->attributes)) {
            return $this->attributes[$key];
        }

        return parent::__get($key);
    }
}




