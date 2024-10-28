<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NavBar extends Model
{
    use HasFactory;
    protected $table = 'nav_bar';

    protected $fillable = [
        'nav_menu_name',
        'nav_menu_link',
        'menu_ordering',
        'deleted_at',
        'created_at',
        'updated_at'
    ];
}
