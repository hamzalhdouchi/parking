<?php

namespace App\Http\Controllers;

use App\Http\Requests\ParkingRequest;
use App\Http\Requests\SearchRequest;
use App\Models\Parking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


/**
 * @OA\Info(
 * title="Parking API",
 * version="1.0",
 * description="Documentation de l'API de gestion des parkings"
 * )
 * 
 * @OA\Tag(
 * name="Parkings",
 * description="Opérations liées aux parkings"
 * )
 */

class ParkingController extends Controller
{
   

        /**
     * @OA\Post(
     * path="/api/parkings",
     * summary="Ajouter un parking",
     * tags={"Parkings"},
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * required={"name", "location", "total_spaces"},
     * @OA\Property(property="name", type="string", example="Parking Central"),
     * @OA\Property(property="location", type="string", example="Paris, France"),
     * @OA\Property(property="total_spaces", type="integer", example=100)
     * )
     * ),
     * @OA\Response(response=201, description="Parking ajouté avec succès"),
     * @OA\Response(response=400, description="Données invalides")
     * )
     */

    public function store(ParkingRequest $request)
    {
        $park = $request->validated();

        $parking = Parking::create($park
    );

        return response()->json(['message' => 'Parking ajouté avec succès', 'parking' => $parking], 201);
    }

    
        /**
     * @OA\Get(
     * path="/api/parkings",
     * summary="Lister tous les parkings",
     * tags={"Parkings"},
     * @OA\Response(
     * response=200,
     * description="Liste des parkings",
     * @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Parking"))
     * )
     * )
     */


    public function index()
    {
        return response()->json(Parking::all(), 200);
    }


        /**
     * @OA\Get(
     * path="/api/parkings/{id}",
     * summary="Obtenir les détails d'un parking",
     * tags={"Parkings"},
     * @OA\Parameter(
     * name="id",
     * in="path",
     * required=true,
     * description="ID du parking",
     * @OA\Schema(type="integer")
     * ),
     * @OA\Response(response=200, description="Détails du parking"),
     * @OA\Response(response=404, description="Parking non trouvé")
     * )
     */
    public function show($id)
    {
        $parking = Parking::find($id);
        if (!$parking) {
            return response()->json(['message' => 'Parking non trouvé'], 404);
        }

        return response()->json($parking, 200);
    }

    
        /**
     * @OA\Put(
     * path="/api/parkings/{id}",
     * summary="Mettre à jour un parking",
     * tags={"Parkings"},
     * @OA\Parameter(
     * name="id",
     * in="path",
     * required=true,
     * description="ID du parking",
     * @OA\Schema(type="integer")
     * ),
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * required={"name", "location", "total_spaces"},
     * @OA\Property(property="name", type="string", example="Parking Central"),
     * @OA\Property(property="location", type="string", example="Paris, France"),
     * @OA\Property(property="total_spaces", type="integer", example=120)
     * )
     * ),
     * @OA\Response(response=200, description="Parking mis à jour"),
     * @OA\Response(response=404, description="Parking non trouvé")
     * )
     */


    public function update(ParkingRequest $request, $id)
    {
        $parking = Parking::find($id);
        if (!$parking) {
            return response()->json(['message' => 'Parking non trouvé']);
        }

        $request->validated();

        $parking->update($request->all());

        return response()->json(['message' => 'Parking mis à jour', 'parking' => $parking]);
    }

        /**
     * @OA\Delete(
     * path="/api/parkings/{id}",
     * summary="Supprimer un parking",
     * tags={"Parkings"},
     * @OA\Parameter(
     * name="id",
     * in="path",
     * required=true,
     * description="ID du parking",
     * @OA\Schema(type="integer")
     * ),
     * @OA\Response(response=200, description="Parking supprimé"),
     * @OA\Response(response=404, description="Parking non trouvé")
     * )
     */

    public function destroy($id)
    {
        $parking = Parking::find($id);
        if (!$parking) {
            return response()->json(['message' => 'Parking non trouvé']);
        }
        $parking->delete();

        return response()->json(['message' => 'Parking supprimé']);
    }

        /**
     * @OA\Get(
     * path="/api/parkings/search",
     * summary="Rechercher un parking par emplacement",
     * tags={"Parkings"},
     * @OA\Parameter(
     * name="location",
     * in="query",
     * required=true,
     * description="Emplacement recherché",
     * @OA\Schema(type="string")
     * ),
     * @OA\Response(response=200, description="Liste des parkings trouvés"),
     * @OA\Response(response=404, description="Aucun parking trouvé")
     * )
     */

    public function search(SearchRequest $request)
        {
            $request->validated();

            $location = $request->location;

            $parkings = Parking::where('location', 'LIKE', "%{$location}%")
                ->withCount(['reservation as espace_ouccepe' => function ($query) {
                    $query->where('heurs_départ', '>=', now()); 
                }])
                ->get()
                ->map(function ($parking) {
                    $parking->available_spaces = $parking->total_spaces - $parking->espace_ouccepe;
                    return $parking;
                });

            if ($parkings->isEmpty()) {
                return response()->json(['message' => 'Aucun parking trouvé dans cette zone'], 404);
            }

            return response()->json($parkings, 200);
        }

            /**
     * @OA\Get(
     * path="/api/parkings/stats",
     * summary="Obtenir les statistiques des parkings",
     * tags={"Parkings"},
     * @OA\Response(
     * response=200,
     * description="Statistiques des parkings",
     * @OA\JsonContent(
     * @OA\Property(property="total_parkings", type="integer", example=10),
     * @OA\Property(property="total_places", type="integer", example=500),
     * @OA\Property(property="reservations_en_cours", type="integer", example=50),
     * @OA\Property(property="taux_occupation", type="string", example="10%")
     * )
     * )
     * )
     */
        public function stats()
            {
                $totalParkings = Parking::count(); 
                $totalPlaces = Parking::sum('total_spaces'); 
                $reservationsEnCours = DB::table('reservations')
                    ->where('heurs_départ', '>=', now())
                    ->count(); 

                $tauxOccupation = $totalPlaces > 0 
                    ? round(($reservationsEnCours / $totalPlaces) * 100, 2)
                    : 0;

                return response()->json([
                    'total_parkings' => $totalParkings,
                    'total_places' => $totalPlaces,
                    'reservations_en_cours' => $reservationsEnCours,
                    'taux_occupation' => $tauxOccupation . '%'
                ]);
            }


}
