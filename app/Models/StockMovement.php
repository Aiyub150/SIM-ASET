<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMovement extends Model
{
    // Kita menolak fitur update, tapi untuk create via Eloquent kita butuh fillable
    protected $fillable = [
        'reference_code',
        'item_id',
        'user_id',
        'type',
        'qty',
        'balance_before',
        'balance_after',
        'notes'
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}