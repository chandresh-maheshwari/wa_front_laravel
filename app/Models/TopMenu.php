<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TopMenu extends Model
{
    use HasFactory;
    protected $table = 'top_menu';

    protected $fillable = [
        'site_logo_img',
        'mts_logo_img',
        'site_logo_img_link',
        'mts_logo_img_link',
        'mts_group_text1',
        'mts_group_text2',
        'deleted_at',
        'created_at',
        'updated_at'
    ];
}
