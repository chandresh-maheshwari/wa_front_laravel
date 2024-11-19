<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;


class DynamicPost extends Model
{
    use HasFactory;

    protected $table = 'dynamic_post';

    protected $fillable = [
        'post_title	',
        'post_description',
        'status',
        'deleted_at',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'post_description' => 'array',
    ];

    public static function boot()
    {
        parent::boot();

        static::saving(function ($dynamicPost) {

            $dynamicPost->slug = Str::slug(str_replace(' ', '_', $dynamicPost->post_title));
        });
    }

    public function savePost($data)
    {
        $this->post_title = $data['post_title'];
        $this->post_description = $data['post_description'];
        return $this->save();
    }
}
