<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    use HasFactory;
    protected $table = 'bank';
    protected $fillable =[

        "title", "contact", "is_active"
    ];
    public function payment()
    {
    	return $this->hasMany('App\Payment');
    }
}
 