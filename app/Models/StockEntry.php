<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class StockEntry extends Model
{
    use HasFactory, LogsActivity;

    protected $guarded = ['id'];
    protected $table = 'stock_entries';
    /**
     * Configure Activity Log
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->useLogName('stock_entry')
            ->setDescriptionForEvent(fn(string $eventName) => "Stock entry has been {$eventName}");
    }

    /**
     * Define Relationships
     */
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function section()
    {
        return $this->belongsTo(WarehouseSection::class, 'section_id');
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function itemUom()
    {
        return $this->belongsTo(ItemUom::class, 'item_uom_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope Filters
     */
    public function scopeFilter($query, $search)
    {
        return $query->when($search ?? false, function ($query, $search) {
            return $query->whereHas('item', function ($q) use ($search) {
                $q->where('name', 'like', "%$search%");
            });
        });
    }

    public function scopeWarehouseFilter($query, $warehouseId)
    {
        return $query->when($warehouseId ?? false, function ($query, $warehouseId) {
            return $query->where('warehouse_id', $warehouseId);
        });
    }

    public function scopeSectionFilter($query, $sectionId)
    {
        return $query->when($sectionId ?? false, function ($query, $sectionId) {
            return $query->where('section_id', $sectionId);
        });
    }

    /**
     * Cast Attributes
     */
    protected $casts = [
        'date' => 'date',
        'quantity' => 'decimal:3',
    ];

    /**
     * Auto-Increment Stock on Create
     */
    public static function boot()
    {
        parent::boot();

        static::created(function ($entry) {

            $stock = WarehouseSectionStock::firstOrCreate([
                'warehouse_id' => $entry->warehouse_id,
                'section_id' => $entry->section_id,
                'item_id' => $entry->item_id,
            ]);

            $primaryUom = $entry->item->unitOfMeasurement->id;
            $uomEntry = $entry->itemUom->unitOfMeasurement->id;
            if ($primaryUom !== $uomEntry) {
                $stockQuantity = $entry->quantity * $entry->itemUom->conversion;
            } else {
                $stockQuantity = $entry->quantity;
            }
            if ($entry->type === 'in') {
                $stock->increment('current_stock', $stockQuantity);
            } elseif ($entry->type === 'out') {
                $stock->decrement('current_stock', $stockQuantity);
            }
        });

        /* static::deleting(function ($entry) {
            $stock = WarehouseSectionStock::where([
                'warehouse_id' => $entry->warehouse_id,
                'section_id' => $entry->section_id,
                'item_id' => $entry->item_id,
            ])->first();

            if ($stock) {
                if ($entry->type === 'in') {
                    $stock->decrement('current_stock', $entry->quantity);
                } elseif ($entry->type === 'out') {
                    $stock->increment('current_stock', $entry->quantity);
                }
            }
        }); */
    }
}
