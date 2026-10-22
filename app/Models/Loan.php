<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Loan extends Model
{
    protected $fillable = [
        'loan_code', 
        'borrower_id', 
        'user_id', 
        'borrow_date', 
        'due_date', 
        'return_date', 
        'status', 
        'notes',
        'borrow_proof_image',
        'return_proof_image',
    ];

    // Casting sangat penting agar tanggal otomatis menjadi instance Carbon
    protected $casts = [
        'borrow_date' => 'date',
        'due_date' => 'date',
        'return_date' => 'date',
    ];

    /**
     * Aksesor kalkulasi status presisi:
     * - Aktif: masih berjalan dan belum jatuh tempo
     * - Terlambat: belum selesai dan melewati jatuh tempo
     * - Selesai: sudah dikembalikan tepat waktu
     * - Selesai Terlambat: sudah dikembalikan tetapi melewati jatuh tempo
     */
    public function getComputedStatusAttribute(): string
    {
        $today = now()->startOfDay();
        $dueDate = $this->due_date ? $this->due_date->copy()->startOfDay() : null;

        // Cek apakah sudah berstatus completed atau semua item sudah kembali
        $isCompleted = ($this->status === 'completed');
        if (!$isCompleted && $this->relationLoaded('loanItems')) {
            $isCompleted = $this->loanItems->count() > 0 && $this->loanItems->every(fn($item) => $item->remaining_qty == 0);
        }

        if ($isCompleted) {
            $returnDate = $this->return_date ? $this->return_date->copy()->startOfDay() : null;
            if ($dueDate && $returnDate && $returnDate->gt($dueDate)) {
                return 'completed_late'; // Selesai Terlambat
            }
            return 'completed'; // Selesai
        }

        // Jika belum selesai:
        if ($dueDate && $today->gt($dueDate)) {
            return 'overdue'; // Terlambat
        }

        return 'active'; // Aktif
    }

    public function borrower(): BelongsTo
    {
        return $this->belongsTo(Borrower::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function loanItems(): HasMany
    {
        return $this->hasMany(LoanItem::class);
    }
}