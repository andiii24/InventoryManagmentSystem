<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProformaContainer extends Model
{
    use HasFactory;
    protected $table = 'proforma_containers';
    protected $fillable =[
        "proforma_id", "container_number", "user_id"
    ];
    public function proforma()
    {
    	return $this->belongsTo('App\Proforma');
    } 
}
