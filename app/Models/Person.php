<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 *
 * @OA\Schema(
 *     required={"name", "last_name", "phone", "curp", "address"},
 *     @OA\Xml(name="Person"),
 *     @OA\Property(property="id", type="integer", readOnly="true", example="1"),
 *     @OA\Property(property="name", type="string", maxLength=32, example="John"),
 *     @OA\Property(property="last_name", type="string", maxLength=32, example="Doe"),
 *     @OA\Property(property="phone", type="string", maxLength=32, example="+52 5544332211"),
 *     @OA\Property(property="curp", type="string", maxLength=32, example="GGSS923211HDFBRR01"),
 *     @OA\Property(property="address", type="string", maxLength=255, example="Av Elementia 12, Arcos de Vallarta, Guadalajara, Jalisco, Mexico"),
 *     @OA\Property(property="created_at", type="string", readOnly="true", format="date-time", description="Datetime marker of verification status", example="2019-02-25 12:59:20"),
 *     @OA\Property(property="updated_at", type="string", readOnly="true", format="date-time", description="Datetime marker of verification status", example="2019-02-25 12:59:20"),
 *     @OA\Property(property="deleted_at",type="string", readOnly="true", format="date-time", description="Datetime marker of verification status", example="2019-02-25 12:59:20")
 * )
 *
 * Class Person
 *
 */
class Person extends Model {
    use HasFactory;

    protected $fillable = ['name', 'last_name', 'phone', 'curp', 'address', 'user_id' ];

    protected $hidden = [
        'status',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

}