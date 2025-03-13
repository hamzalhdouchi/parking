<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'parking_id',
        'user_id',
        'heurs_arrivée',
        'heurs_départ',
    ];

    public function parking()
    {
        return $this->belongsTo(Parking::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
