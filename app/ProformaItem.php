<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProformaItem extends Model
{ 
    use HasFactory;
    protected $table = 'proformaitems';
    protected $fillable =[
        "proforma_id", "description", "qty",  "unit_price", "total_amount","user_id","is_active"
    ];
    public function proforma()
    {
    	return $this->belongsTo('App\Proforma');
    } 
}
