<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model {
    use HasFactory;


    protected $fillable = ['source_account', 'destination_account', 'user_id', 'operation_type', 'amount', 'concept', 'reference', 'transaction_date', 'status'];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function sourceAccount(): BelongsTo {
        return $this->belongsTo(Account::class, 'source_account');
    }

    public function destinationAccount(): BelongsTo {
        return $this->belongsTo(Account::class, 'destination_account');
    }

}
