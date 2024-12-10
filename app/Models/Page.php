<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;



class Page extends Model
{
    use HasFactory;
    protected $table = 'dynamic_page';

    protected $fillable = [
        'post_type',
        'page_name',
        'page_description',
        'image',
        'ordering',
        'status',
        'deleted_at',
        'created_at',
        'updated_at'
    ];
    public static function boot()
    {
        parent::boot();

        static::saving(function ($dynamicPage) {

            $dynamicPage->slug = Str::slug(str_replace(' ', '_', $dynamicPage->page_name));
        });
    }


}