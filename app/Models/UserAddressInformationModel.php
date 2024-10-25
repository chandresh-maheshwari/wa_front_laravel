<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAddressInformationModel extends Model
{
    protected $table = 'user_address_information';

    protected $fillable = [
        'university_student',
        'instructions',
        'walk_up',
        'health_protection',
       
    ];
}
