<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    protected $fillable =[

        "name", "phone", "email", "address", "is_active"
    ];

    public function product()
    {
    	return $this->hasMany('App\Product');
    	
    }
    public function vehicle()
    {
    	return $this->hasMany('App\Vehicle');
    	
    }
    public function manufacture()
    { 
    	return $this->hasMany('App\Manufacture');
    	
    }
}
