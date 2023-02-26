<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable =[
        "name", "code", "type", "brand_id", "category_id", "unit_id","dozen_no", "carton_no", "piece_in_carton", "cost", "price", "qty", "alert_quantity", "tax_id", "tax_method","warehouse_id", "featured", "product_details", "is_active","user_id"
    ];

    public function category()
    {
    	return $this->belongsTo('App\Category');
    }
    public function warehouse()
    { 
    	return $this->belongsTo('App\Warehouse');
    }
    public function brand()
    {
    	return $this->belongsTo('App\Brand');
    }

    public function variant()
    {
        return $this->belongsToMany('App\Variant', 'product_variants')->withPivot('id', 'item_code', 'additional_price');
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
            ['featured', 1]
        ]);
    }
}
