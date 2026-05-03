<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rab extends Model
{
    protected $fillable = ['project_name', 'customer_name', 'date', 'total_amount'];

    public function items()
    {
        return $this->hasMany(RabItem::class);
    }
}
