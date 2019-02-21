<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
	public $timestamps = true;

	protected $fillable = ['restaurant_id'];

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }	

    public function orderProducts()
    {
        return $this->hasMany(OrderProduct::class);
    }
}