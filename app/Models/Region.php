<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Region extends Model
{
     use HasFactory;
     use SoftDeletes;

      protected $table = 'regions';

       protected $fillable = [
        'name', 
        'code', 
       
       ];

        public function properties()
    {
        return $this->hasMany(Property::class);
    }
}
