<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImproveEnvirmentalProtection extends Model
{
    use HasFactory;
    protected $table = 'improve_envirmental_protection';

    protected $fillable = [
        'protections_description',
        'button_name',
        'button_link',
        'active',
        'deleted_at',
        'created_at',
        'updated_at'
    ];
}
