<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $table = 'sales';
    protected $fillable =[
        "reference_no", "user_id",  "customer_id", "warehouse_id", "bank_id", "type",   "item", "total_qty",  "total_tax", "total_price",  "grand_total",  "sale_status", "payment_status", "paid_amount", "sale_note", "staff_note", "created_at"
    ];

    public function biller()
    {  
    	return $this->belongsTo('App\Biller');
    }

    public function customer()
    {
    	return $this->belongsTo('App\Customer');
    }

    public function warehouse()
    {
    	return $this->belongsTo('App\Warehouse');
    }

    public function user()
    {
    	return $this->belongsTo('App\User');
    }
}
