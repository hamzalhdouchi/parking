<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Reservation;

    /**
     * @OA\Schema(
     *     schema="Parking",
     *     title="Parking",
     *     description="Modèle d'un parking",
     *     required={"name", "location", "total_spaces"},
     *     @OA\Property(property="id", type="integer", example=1),
     *     @OA\Property(property="name", type="string", example="Parking Central"),
     *     @OA\Property(property="location", type="string", example="Paris, France"),
     *     @OA\Property(property="total_spaces", type="integer", example=100),
     *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-03-13T12:00:00Z"),
     *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-03-13T12:30:00Z")
     * )
     */
class Parking extends Model
{
    
    protected $fillable = [
        'name',
        'location',
        'total_spaces',
        'image'
    ];

    public function reservation()
    {
        return $this->hasMany(Reservation::class);
    }
}
