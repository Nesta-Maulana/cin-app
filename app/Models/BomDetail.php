<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;

class BomDetail extends Model
{
    use HasFactory, LogsActivity;
    protected $table = 'bom_details';
    protected $guarded = ['id'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
    }

    /**
     * Get the user that owns the BomDetail
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function bom()
    {
        return $this->belongsTo(BOM::class, 'bom_id', 'id');
    }
    public function material()
    {
        return $this->belongsTo(Material::class, 'material_id', 'id');
    }
    public function materialPrice()
    {
        return $this->belongsTo(MaterialPrice::class, 'material_price', 'id');
    }

}
