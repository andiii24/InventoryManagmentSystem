<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proforma extends Model
{
    use HasFactory;
    protected $table = 'proformas';
    protected $fillable =[
        "supplier_name", "buyer_name", "order_number",  "pfi_number", "pfi_date", "bank_name", "payment_term", "user_id", "is_active"
    ]; 
    public function item()
    {
    	return $this->hasMany('App/ProformaItem');
    	
    }
    public function proformacount()
    {
    	return $this->hasOne('App/ProformaCount','id');
    	
    }
    public function user()
    {
    	return $this->belongsTo('App\User');
    }
    public function container()
    {
    	return $this->hasMany('App/ProformaContainer');
    	
    }
}
