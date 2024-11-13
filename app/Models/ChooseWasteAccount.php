<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChooseWasteAccount extends Model
{
    use HasFactory;
    protected $table = 'choose_waste_account';

    protected $fillable = [
        'name',
        'account_title',
        'account_description',
        'active',
        'deleted_at',
        'created_at',
        'updated_at'
    ];
}
