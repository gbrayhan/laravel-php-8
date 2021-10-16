<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Card extends Model {
    use HasFactory;

    protected $fillable = ['card_number', 'user_id', 'type', 'status',];

    public function user() {
        return $this->belongsTo(User::class);
    }


}
