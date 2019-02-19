<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Restaurant extends Model
{
	public $timestamps = true;

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}