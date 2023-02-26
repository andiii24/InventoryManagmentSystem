<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product_Sale extends Model
{
	protected $table = 'product_sales';
    protected $fillable =[ 
        "sale_id", "product_id","type",  "qty", "sell_unit","product_unit","price", "net_unit_price", "tax_rate", "tax", "total"
    ];
}
