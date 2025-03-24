<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ManualItemRequest extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function customerOrder()
    {
        return $this->belongsTo(CustomerOrder::class);
    }

    /**
     * Get all details for this item request.
     */
    public function details()
    {
        return $this->hasMany(ManualItemRequestDetail::class);
    }

}
