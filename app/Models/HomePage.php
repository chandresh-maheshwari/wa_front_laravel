<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HomePage extends Model
{
    use HasFactory;
    protected $table = 'home_page';

    protected $fillable = [
        'home_section_img',
        'home_section_title',
        'home_section_description',
        'home_section_button_name',
        'home_section_button_name_link',
        'deleted_at',
        'created_at',
        'updated_at'
    ];
}
