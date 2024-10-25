<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestimonialModel extends Model
{
    use HasFactory;
    protected $table = 'testimonials';

    protected $fillable = [
        'testimonial',
        'add_by',
        'position',
        'created_at',
        'updated_at'     

    ];
}
