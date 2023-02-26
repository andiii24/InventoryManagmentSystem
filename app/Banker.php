<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banker extends Model
{
    use HasFactory;
    protected $table = 'banks';
    protected $fillable =[
        "name", "user_id", "is_active"
    ];
}
