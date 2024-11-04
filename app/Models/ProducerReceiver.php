<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProducerReceiver extends Model
{
    use HasFactory;
    protected $table = 'producers_and_receivers_section';

    protected $fillable = [
        'section_title',
        'section_image',
        'producer_title',
        'producer_description',
        'receiver_title',
        'receiver_description',
        'deleted_at',
        'created_at',
        'updated_at'
    ];
}
