<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProformaCount extends Model
{
    use HasFactory;
    protected $table = 'proformacounts';
    protected $fillable =[
        "pro_id", "purchase_items", "purchase_qty"
    ]; 
    public function item()
    {
    	return $this->hasMany('App/ProformaItem');
    	
    }
    public function proforma()
    {
    	return $this->belongsTo('App\Proforma','pro_id');
    }
    public function vehicleinfo()
    {
    	return $this->hasMany('App/VehicleInfo');
    	
    }
    public function product()
    {
    	return $this->hasMany('App/Product');
    	
    }
}
