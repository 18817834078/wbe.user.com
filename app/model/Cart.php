<?php

namespace App\model;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $fillable=[
        'user_id','goods_id','amount'
    ];

    public function menu(){
        return $this->belongsTo('App\model\Menu','goods_id','id');
    }

}
