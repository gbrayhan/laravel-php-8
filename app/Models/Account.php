<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model {
    use HasFactory;

    protected $fillable = ['account_number', 'person_id', 'product', 'balance', 'nip', 'status'];

    public function person() {
        return $this->belongsTo(Person::class);
    }


}
