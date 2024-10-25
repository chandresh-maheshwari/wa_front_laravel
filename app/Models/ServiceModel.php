<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ServiceModel extends Model
{
    use HasFactory;
    use HasFactory;
    protected $table = 'service';

    protected $fillable = [
        'service_title',
        'description',
        'created_at',
        'updated_at'

    ];

    static function getservicedata($searchValue, $columnName, $columnSortOrder, $draw, $row, $rowperpage)
    {
        // $sql = DB::table('add_vehicle');



        $sql = DB::table('service');
        // dd($sql);
        $sql->select(
            'service.id as idd',
            'service_title',
            'description',
        );

        // return $sql;
        // dd($sql);

        if ($searchValue != '') {
            $sql->where(function ($query) use ($searchValue) {
                $query
                    ->orWhere('service_title', 'LIKE', "%{$searchValue}%")
                    ->orWhere('description', 'LIKE', "%{$searchValue}%");
            });
        }
        $query =  $sql->skip($row)->take($rowperpage)->get();


        if ($columnName && $columnSortOrder) {
            if ($columnName == 'service_title') {
                $query = $sql->orderby('service.service_title', $columnSortOrder);
            } else if ($columnName == 'description') {
                $query = $sql->orderby('service.description', $columnSortOrder);
            }
        }
        $query = $sql->orderByDesc('service.id')->get();
        return $query;
    }

    static function getserviceTotal($searchValue, $columnName, $columnSortOrder, $draw, $row, $rowperpage)
    {
        $sql = DB::table('service');
        if ($searchValue != '') {
            $sql->where(function ($query) use ($searchValue) {
                $query->where('service_title', 'LIKE', "%{$searchValue}%")
                    ->orWhere('description', 'LIKE', "%{$searchValue}%");
            });
        }
        $query = $sql->count();
        return $query;
    }
}
