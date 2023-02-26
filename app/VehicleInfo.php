<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleInfo extends Model
{
    use HasFactory; 
    protected $table = 'vehicleinfo';
    protected $fillable =[
        "name", "code", "category_id", "brand_id", "cost", "warehouse_id", "detail", "user_id","is_active"
    ]; 
    public function vehicle()
    {   
    	return $this->hasMany('App\Vehicle');
    }
    public function manufacture()
    {   
    	return $this->hasMany('App\Manufacture');
    }
    public function product()
    {   
    	return $this->hasMany('App\VehicleProduct');
    }
    public function category()
    {
    	return $this->belongsTo('App\VehicleCategory','id');
    }
    public function brand()
    {
    	return $this->belongsTo('App\VehicleBrand', 'id');
    }
}
