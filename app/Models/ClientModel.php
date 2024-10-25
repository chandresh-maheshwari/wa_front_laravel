<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class ClientModel extends Model
{
    use HasFactory;
    protected $table = 'client';

    protected $fillable = [
        'partner_name',
        'partner_logo',
        'created_at',
        'updated_at'     

    ];

    // dd("czxczxczxc");
    static function getclientdata($searchValue, $columnName, $columnSortOrder, $draw, $row, $rowperpage)
    {
        // $sql = DB::table('add_vehicle');

      
        $sql = DB::table('client');
        // dd($sql);
        $sql->select(
            'client.id as idd',
            'partner_name',
            'partner_logo',  
        );

        // return $sql;
// dd($sql);
         
        if ($searchValue != '') {
            $sql->where(function ($query) use ($searchValue) {
                $query
                    ->orWhere('partner_name', 'LIKE', "%{$searchValue}%")
                    ->orWhere('partner_logo', 'LIKE', "%{$searchValue}%");
                   
            });
        }
        $query =  $sql->skip($row)->take($rowperpage)->get();


        if ($columnName && $columnSortOrder) {
            if ($columnName == 'partner_name') {
               $query = $sql->orderby('client.partner_name', $columnSortOrder);
           } else if ($columnName == 'partner_logo') {
               $query = $sql->orderby('client.partner_logo', $columnSortOrder);
           } 
       }
       $query = $sql->orderByDesc('client.id')->get();
       return $query;
}
static function getclientTotal($searchValue, $columnName, $columnSortOrder, $draw, $row, $rowperpage)
{
    $sql = DB::table('client');
    if ($searchValue != '') {
        $sql->where(function ($query) use ($searchValue) {
            $query->where('partner_name', 'LIKE', "%{$searchValue}%")
                ->orWhere('partner_logo', 'LIKE', "%{$searchValue}%");
              
                
        });
    }
        $query = $sql->count();
    return $query;
}

}