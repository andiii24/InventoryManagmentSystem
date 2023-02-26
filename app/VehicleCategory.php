<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleCategory extends Model
{
    use HasFactory;
    protected $table = 'vehiclecategory';
    protected $fillable =[

        "name", "parent_id", "is_active", 'user_id'
    ];
    public function vehicle()
    {
    	return $this->hasMany('App\Vehicle');
    }
    public function vehicleinfo()
    {
    	return $this->hasMany('App/VehicleInfo','category_id');

    } 
    public function product()
    {
    	return $this->hasMany('App\VehicleProduct', 'category_id');

    } 
}
 