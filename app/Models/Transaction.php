<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;



/**
 *
 * @OA\Schema(
 *     required={"source_account", "destinatio_account", "operation_type", "amount", "concept", "reference"},
 *     @OA\Xml(name="Transaction"),
 *     @OA\Property(property="id", type="integer", readOnly="true", example="1"),
 *     @OA\Property(property="source_account", type="string", maxLength=100, example="234421A123342"),
 *     @OA\Property(property="destinatio_account", type="string", maxLength=100, example="234421A123342"),
 *     @OA\Property(property="user_id", type="integer", readOnly="false", example="1"),
 *     @OA\Property(property="operation_type", type="string", maxLength=32, example="abono"),
 *     @OA\Property(property="amount", type="decimal", maxLength=32, example="120.9"),
 *     @OA\Property(property="concept", type="string", maxLength=100, example="Pago de servicio"),
 *     @OA\Property(property="reference", type="string", maxLength=100, example="KJ1231234A"),
 *     @OA\Property(property="transaction_date", type="string", readOnly="false", format="date-time", description="Datetime marker of verification status", example="2019-02-25 12:59:20"),
 *     @OA\Property(property="status", type="string", readOnly="false", description="Status of the transaction", example="pending"),
 *     @OA\Property(property="created_at", type="string", readOnly="true", format="date-time", description="Datetime marker of verification status", example="2019-02-25 12:59:20"),
 *     @OA\Property(property="updated_at", type="string", readOnly="true", format="date-time", description="Datetime marker of verification status", example="2019-02-25 12:59:20"),
 *     @OA\Property(property="deleted_at",type="string", readOnly="true", format="date-time", description="Datetime marker of verification status", example="2019-02-25 12:59:20")
 * )
 *
 * Class Transaction
 *
 */
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
