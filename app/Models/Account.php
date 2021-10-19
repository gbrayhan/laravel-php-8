<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;



/**
 *
 * @OA\Schema(
 *     required={"account_number", "person_id", "product", "balance"},
 *     @OA\Xml(name="Account"),
 *     @OA\Property(property="id", type="integer", readOnly="true", example="1"),
 *     @OA\Property(property="account_number", type="string", maxLength=100, example="00919222G122"),
 *     @OA\Property(property="person_id", type="integer", readOnly="false", example="2211"),
 *     @OA\Property(property="product", type="string", maxLength=32, example="BBVA CC"),
 *     @OA\Property(property="created_at", type="string", readOnly="true", format="date-time", description="Datetime marker of verification status", example="2019-02-25 12:59:20"),
 *     @OA\Property(property="updated_at", type="string", readOnly="true", format="date-time", description="Datetime marker of verification status", example="2019-02-25 12:59:20"),
 *     @OA\Property(property="deleted_at",type="string", readOnly="true", format="date-time", description="Datetime marker of verification status", example="2019-02-25 12:59:20")
 * )
 *
 * Class Account
 *
 */
class Account extends Model {
    use HasFactory;

    protected $fillable = ['account_number', 'person_id', 'product'];

    protected $hidden = [
        'nip',
        'balance',
        'status',
    ];

    public function person() {
        return $this->belongsTo(Person::class);
    }


}
