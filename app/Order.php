<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
	public $timestamps = true;

    public function orderProducts()
    {
        return $this->hasMany(OrderProduct::class);
    }
}