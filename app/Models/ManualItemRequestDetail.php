<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ManualItemRequestDetail extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    public function manualItemRequest()
    {
        return $this->belongsTo(ManualItemRequest::class);
    }
}
