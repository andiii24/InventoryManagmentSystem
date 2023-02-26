<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;
    protected $table = 'vehicles';
    protected $fillable =[
    "vehicle_id", "name", "code", "vehicle_brand_id",   "vehicle_category_id", "chassis_no","engine_no", "cost",   "tax_id", "tax_method", "warehouse_id",  "product_details",  "is_active","user_id"
    ];
    public function category() 
    {
    	return $this->belongsTo('App\VehicleCategory');
    } 
    public function brand()
    {
    	return $this->belongsTo('App\VehicleBrand');
    }
    public function vehicleinfo()
    {
    	return $this->belongsTo('App\VehicleInfo');
    }
    public function warehouse()
    {
    	return $this->belongsTo('App\Warehouse');
    } 


}