<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReservationRequest;
use App\Http\Requests\UpdateReservationRequest;
use Illuminate\Http\Request;
use App\Models\Reservation;
use App\Models\Parking;
use Illuminate\Support\Facades\Auth;

/**
 * 
 * @/tag(
 * name="Reservations",
 * Description="les reservation "
 * )
 */

class ReservationController extends Controller
{

        /**
     * @OA\Post(
     * path="/reservations",
     * summary="Créer une réservation",
     * tags={"Reservations"},
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * required={"user_id", "parking_id", "heurs_arrivée", "heurs_départ"},
     * @OA\Property(property="user_id", type="integer", example=1),
     * @OA\Property(property="parking_id", type="integer", example=3),
     * @OA\Property(property="heurs_arrivée", type="string", format="date-time", example="2025-03-15T08:00:00Z"),
     * @OA\Property(property="heurs_départ", type="string", format="date-time", example="2025-03-15T10:00:00Z")
     * )
     * ),
     * @OA\Response(response=200, description="Réservation créée"),
     * @OA\Response(response=400, description="Conflit avec une autre réservation")
     * )
     */
    public function create(ReservationRequest $request)
    {
        
        $request->validated();
        
        $userId = $request->user_id; 
        if (is_null($userId)) {
            return response()->json(['message' => 'User ID is required']);
        }
        
        $existingReservation = Reservation::where('parking_id', $request->parking_id)
            ->where('user_id', $userId)  
            ->where(function($query) use ($request) {
                $query->whereBetween('heurs_arrivée', [$request->heurs_arrivée, $request->heurs_départ])
                    ->orWhereBetween('heurs_départ', [$request->heurs_arrivée, $request->heurs_départ]);
            })
            ->exists();
        

            

        if ($existingReservation) {
            return response()->json(['message' => 'had reservation dega dayza']);
        }

        $reservation = Reservation::create([
            'parking_id' => $request->parking_id,
            'user_id' => $request->user_id, 
            'heurs_arrivée' => $request->heurs_arrivée,
            'heurs_départ' => $request->heurs_départ,
        ]);

        return response()->json([
            'message' => 'Réservation effectuée avec succès.',
            'reservation' => $reservation
        ]);
    }


    /**
     * @OA\Put(
     * path="/reservations/{id}",
     * summary="Modifier une réservation",
     * tags={"Reservations"},
     * @OA\Parameter(
     * name="id",
     * in="path",
     * required=true,
     * @OA\Schema(type="integer")
     * ),
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * @OA\Property(property="heurs_arrivée", type="string", format="date-time"),
     * @OA\Property(property="heurs_départ", type="string", format="date-time")
     * )
     * ),
     * @OA\Response(response=200, description="Réservation mise à jour"),
     * @OA\Response(response=400, description="Conflit avec une autre réservation"),
     * @OA\Response(response=404, description="Réservation non trouvée")
     * )
     */
    
    public function update(UpdateReservationRequest $request, $id)
    {
        $reservation = Reservation::find($id);
    
        if (!$reservation) {
            return response()->json(['message' => 'Cette réservation n existe pas.']);
        }
    
        $request->validated();
    
        
        $existingReservation = Reservation::where('parking_id', $reservation->parking_id)
            ->where('user_id', $id)  
            ->where(function($query) use ($request) {
                $query->whereBetween('heurs_arrivée', [$request->heurs_arrivée, $request->heurs_départ])
                      ->orWhereBetween('heurs_départ', [$request->heurs_arrivée, $request->heurs_départ]);
            })
            ->exists();
    
        if ($existingReservation) {
            return response()->json(['message' => 'Une autre réservation existe déjà pour cette période.'], 400);
        }
    
        
        $reservation->update([
            'heurs_arrivée' => $request->heurs_arrivée,
            'heurs_départ' => $request->heurs_départ,
        ]);
    
        return response()->json([
            'message' => 'Réservation mise à jour avec succès.',
            'reservation' => $reservation
        ]);
    }

    /**
     * @OA\Delete(
     * path="/reservations/{id}",
     * summary="Supprimer une réservation",
     * tags={"Reservations"},
     * @OA\Parameter(
     * name="id",
     * in="path",
     * required=true,
     * @OA\Schema(type="integer")
     * ),
     * @OA\Response(response=200, description="Réservation supprimée"),
     * @OA\Response(response=403, description="Non autorisé"),
     * @OA\Response(response=404, description="Réservation non trouvée")
     * )
     */
    public function destroy($id)
        {
            $reservation = Reservation::find($id);

            if (!$reservation) {
                return response()->json(['message' => 'Cette réservation n existe pas.'], 404);
            }
            $reservation->delete();
            return response()->json(['message' => 'Reservation annulée avec succes.'], 200);
        }

        /**
         * @OA\Get(
         * path="/reservations",
         * summary="Récupérer les réservations",
         * tags={"Reservations"},
         * @OA\Response(response=200, description="Liste des réservations")
         * )
         */
        public function index($id)
        {
            $userId = $id; 
        
            $reservationsActuele = Reservation::where('user_id', $userId)
                ->where('heurs_départ', '>=', now()) 
                ->orderBy('heurs_arrivée', 'asc')
                ->get();
        
            $reservationsPasse = Reservation::where('user_id', $userId)
                ->where('heurs_départ', '<', now()) 
                ->orderBy('heurs_arrivée', 'desc')
                ->get();
        
            return response()->json([
                'reservations_actuelles' => $reservationsActuele,
                'reservations_passees' => $reservationsPasse
            ]);
        }
        
    
}

