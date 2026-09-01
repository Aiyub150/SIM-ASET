<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Borrower extends Model
{
    protected $fillable = ['institution_name', 'pic_name', 'contact_number', 'address'];

    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }
}