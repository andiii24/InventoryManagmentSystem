<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleProduct extends Model
{
    use HasFactory;
    protected $table = 'vehicle_products';
    protected $fillable =[
        "vehicle_id", "name", "code", "brand_id",   "category_id", "chassis_no", "engine_no", "qty", "cost", "price", "tax_id", "tax_method", "warehouse_id","is_active", "user_id"
    ];
    public function category()
    {
    	return $this->belongsTo('App\VehicleCategory','id');
    }
    public function brand()
    {
    	return $this->belongsTo('App\VehicleBrand');
    }
    public function vehicle()
    {
    	return $this->belongsTo('App\VehicleInfo');
    }
    public function warehouse()
    {
    	return $this->belongsTo('App\Warehouse');
    }
    public function scopeActiveStandard($query)
    {
        return $query->where([
            ['is_active', true],
            ['type', 'standard']
        ]);
    }
 
    public function scopeActiveFeatured($query)
    {
        return $query->where([
            ['is_active', true],
            ['qty', '>', 0]
        ]);
    }
}

