<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Property extends Model
{
     use HasFactory;
    use SoftDeletes;

    protected $table = 'properties';

      protected $fillable = [
        'title', 
        'description', 
        'type', 
        'price', 
        'location',
        'region_id',
         'status', 
         'featured_image'
    ];

     public function region()
    {
        return $this->belongsTo(Region::class);
    }
}
