<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactPage extends Model
{
    use HasFactory;
    protected $table = 'contact_page';

    protected $fillable = [
        'title',
        'tagline',
        'name',
        'email',
        'description',
        'button_name',
        'button_name_link',
        'active',
        'deleted_at',
        'created_at',
        'updated_at'
    ];
}
