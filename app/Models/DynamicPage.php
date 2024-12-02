<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class DynamicPage extends Model
{
    use HasFactory;

    protected $table = 'dynamic_page';

    protected $fillable = [
        'page_name	',
        'page_data',
        'ordering',
        'status',
        'deleted_at',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'page_data' => 'array',
    ];

    public static function boot()
    {
        parent::boot();

        static::saving(function ($dynamicPage) {

            $dynamicPage->slug = Str::slug(str_replace(' ', '_', $dynamicPage->page_name));
        });
    }

    public function savePage($data)
    {
        $this->page_name = $data['page_name'];
        $this->page_data = $data['page_data'];
        $this->ordering = $data['ordering']; 
        return $this->save();
    }

}
