<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RabItem extends Model
{
    protected $fillable = ['rab_id', 'description', 'quantity', 'unit_price', 'total_price'];

    public function rab()
    {
        return $this->belongsTo(Rab::class);
    }
}
