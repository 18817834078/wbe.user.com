<?php

namespace App\model;

use Illuminate\Database\Eloquent\Model;

class OrderGood extends Model
{
    protected $fillable=[
        'order_id','goods_id','amount','goods_name','goods_img','goods_price'
    ];

}
