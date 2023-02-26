<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Manufacture extends Model
{
    use HasFactory;
    protected $table = 'manufactures';
    protected $fillable =[
        "vehicle_id", "name", "code", "brand_id", "category_id", "chassis_no",   "engine_no", "qty", "status",  "warehouse_id","is_active", "user_id"
    ];
    public function category()
    {
    	return $this->belongsTo('App\VehicleCategory','id');
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