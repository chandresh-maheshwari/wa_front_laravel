<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AddressAccountInformationModel extends Model
{
    use HasFactory;

    protected $table = 'address_account_information';

    protected $fillable = [
        // 'name',
        // 'email',
        // 'password',
        'userid',
        'address',
        'city',
        'states',
        'contact',
        'pickup_address',
        'date1',
        'time1',
        'date2',
        'time2',
        'date3',
        'time3',
    ];
}
