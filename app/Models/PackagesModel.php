<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PackagesModel extends Model
{
    use HasFactory;
    protected $table = 'packages';

    protected $fillable = [
        'package_title',
        'package_des',
        'package_price',
        'additional_info', 
        'created_at',
        'updated_at',
    ];

    static function getpackagedata($searchValue, $columnName, $columnSortOrder, $draw, $row, $rowperpage)
    {
        // $sql = DB::table('add_vehicle');

      
        $sql = DB::table('packages');
        // dd($sql);
        $sql->select(
            'packages.id as idd',
            'package_title',
            'package_des', 
            'package_price',
            'additional_info',

        );

        // return $sql;
// dd($sql);
         
        if ($searchValue != '') {
            $sql->where(function ($query) use ($searchValue) {
                $query
                    ->orWhere('package_title', 'LIKE', "%{$searchValue}%")
                    ->orWhere('package_des', 'LIKE', "%{$searchValue}%")
                    ->orWhere('package_price', 'LIKE', "%{$searchValue}%")
                    ->orWhere('additional_info  ', 'LIKE', "%{$searchValue}%");
                   
            });
        }
        $query =  $sql->skip($row)->take($rowperpage)->get();


        if ($columnName && $columnSortOrder) {
            if ($columnName == 'package_title') {
               $query = $sql->orderby('packages.package_title', $columnSortOrder);
           } else if ($columnName == 'package_des') {
               $query = $sql->orderby('packages.package_des', $columnSortOrder);
           } else if ($columnName == 'package_price') {
            $query = $sql->orderby('packages.package_price', $columnSortOrder);
        }   else if ($columnName == 'additional_info') {
            $query = $sql->orderby('packages.additional_info', $columnSortOrder);
        }    
       }
       $query = $sql->orderByDesc('packages.id')->get();
       return $query;
}
static function getpackageTotal($searchValue, $columnName, $columnSortOrder, $draw, $row, $rowperpage)
{
    $sql = DB::table('packages');
    if ($searchValue != '') {
        $sql->where(function ($query) use ($searchValue) {
            $query->where('package_title', 'LIKE', "%{$searchValue}%")
                ->orWhere('package_des', 'LIKE', "%{$searchValue}%")
                ->orWhere('package_price', 'LIKE', "%{$searchValue}%")
                ->orWhere('additional_info', 'LIKE', "%{$searchValue}%");
               
                
        });
    }
        $query = $sql->count();
    return $query;
}
}
