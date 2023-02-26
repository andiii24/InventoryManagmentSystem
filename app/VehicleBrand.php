<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleBrand extends Model
{
    use HasFactory;
    protected $table = 'vehiclebrand';
    protected $fillable =[

        "name", "user_id"
    ];
    public function vehicle()
    {
    	return $this->hasMany('App/Vehicle');

    } 
    public function vehicleinfo()
    {
    	return $this->hasMany('App/VehicleInfo','brand_id');

    } 
    public function product()
    {
    	return $this->hasMany('App/VehicleProduct');

    } 
}
