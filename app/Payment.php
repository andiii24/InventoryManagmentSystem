<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable =[
         "user_id", "sale_id", "payment_reference", "amount", "keri", "paying_method", "payment_note","payment_date"
    ];
    public function bank()
    {
    	return $this->belongsTo('App\Bank');
    }
}
