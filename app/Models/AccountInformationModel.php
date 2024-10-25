<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountInformationModel extends Model
{
    use HasFactory;

    protected $table = 'account_information';

    protected $fillable = [
        'name',
        'email',
        'password',
        // 'address',
        // 'city',
        // 'states',
        // 'contact',
        // 'pickup_address',
        // 'date1',
        // 'time1',
        // 'date2',
        // 'time2',
        // 'date3',
        // 'time3',
    ];
}
