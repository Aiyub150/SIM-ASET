<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Item extends Model
{
    protected $fillable = [
        'sku', 
        'ownership_status',
        'owner_name',
        'name', 
        'image',
        'category_id', 
        'location_id', 
        'total_qty', 
        'available_qty', 
        'description'
    ];

    public function isInternal(): bool
    {
        return ($this->ownership_status ?? 'internal') === 'internal';
    }

    public function isVendor(): bool
    {
        return $this->ownership_status === 'vendor';
    }

    public function isExternal(): bool
    {
        return $this->ownership_status === 'external';
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function loanItems(): HasMany
    {
        return $this->hasMany(LoanItem::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }
}