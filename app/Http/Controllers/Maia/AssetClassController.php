<?php

namespace App\Http\Controllers\Maia;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AssetClass;
use Illuminate\Support\Facades\Log;

/**
 * @OA\Info(
 *     title="Documentazione API rest",
 *     version="1.0",
 *     description="Per utilizzare le funzioni update e store, dopo aver incollato il token nell'authorization, cliccare su body, raw, JSON e sarà tutto già preimpostato"
 * )
 * @OA\Server(url="http://localhost"),
 * 
 * @OA\Components(
 *     @OA\Response(
 *         response="200",
 *         description="Richiesta completata con successo"
 *     ),
 *     @OA\Response(
 *         response="400",
 *         description="Richiesta non valida: succede quando uno o più parametri sono errati o mancano, o quando quell'elemento inserito esiste già"
 *     ),
 *     @OA\Response(
 *         response="404",
 *         description="Risorsa non trovata. Questo accade quando quell'elemento non è presente nel database"
 *     )
 * )
 */


class AssetClassController extends Controller
{

    /**
     * @OA\Post(
     *     path="/api/asset_classes",
     *     summary="Crea un'asset class",
     *     tags={"AssetClass"},
     *     @OA\Response(response=200, description="Asset class creata con successo"),
     *     @OA\Response(response=400, description="Asset class già esistente")
     * )
     */
    public function store(Request $request)
{
    // Validazione
    $validatedData = $request->validate([
        'name' => 'required|string|max:255',
        'service_id' => 'required|integer|exists:services,id',
        'commission_id' => 'required|integer|exists:commissions,id',
    ]);

    $existingAsset = AssetClass::where('name', $validatedData['name'])->first();

    if ($existingAsset) {
        return response()->json(['message' => 'Asset class già esistente'], 400);
    }

    $assetClass = AssetClass::create($validatedData);

    return response()->json([
        'message' => 'Asset class creata con successo',
        'data' => $assetClass
    ], 200);
}


    /**
     * @OA\Put(
     *     path="/api/asset_classes/{id}",
     *     summary="Aggiorna un'asset class con l'id specificato nell'URL",
     *     tags={"AssetClass"},
     *     @OA\Response(response=200, description="Update avvenuto con successo"),
     *     @OA\Response(response=404, description="Asset class non trovata"),
     *     @OA\Response(response=400, description="Asset class già esistente")
     * )
     */
    public function update(Request $request, $id)
{
    // Validazione
    $validatedData = $request->validate([
        'name' => 'required|string|max:255',
        'service_id' => 'required|integer|exists:services,id',
        'commission_id' => 'required|integer|exists:commissions,id',
    ]);

    //controllo per vedere se esiste già un asset con il nome modificato
    $existingAsset = AssetClass::where('name', $validatedData['name'])->first();
    if ($existingAsset) {
        return response()->json(['message' => 'Questo nome già esiste'], 400);
    }

    //controllo dell'id
    $assetClass = AssetClass::find($id);
    if (!$assetClass) {
        return response()->json(['message' => 'Asset class inesistente'], 404);
    }

    $assetClass->update($validatedData);

    return response()->json([
        'message' => 'Update avvenuto con successo',
        'data' => $assetClass
    ]);
}

    /**
     * @OA\Delete(
     *     path="/api/asset_classes/{id}",
     *     summary="Elimina l'asset class con l'id specificato",
     *     tags={"AssetClass"},
     *     @OA\Response(response=200, description="Asset class eliminata con successo"),
     *     @OA\Response(response=404, description="Asset class non trovata")
     * )
     */
    public function destroy($id)
    {
        $assetClass = AssetClass::find($id);
        if ($assetClass) {
            $assetClass->delete();
            return response()->json(['message' => 'Asset class eliminata con successo']);
        } else {
            return response()->json(['message' => 'Asset class non trovata'], 404);
        }
    }

    /**
 * @OA\Get(
 *     path="/api/asset-classes",
 *     summary="Torna una lista di asset classes con i filtri specificati",
 *     tags={"AssetClass"},
 *     @OA\Parameter(
 *         name="id",
 *         in="query",
 *         description="Filtro per ID dell'asset class",
 *         required=false,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Parameter(
 *         name="asset_name",
 *         in="query",
 *         description="Filtro per nome dell'asset class (case insensitive)",
 *         required=false,
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Parameter(
 *         name="service_name",
 *         in="query",
 *         description="Filtro per nome del servizio (case insensitive)",
 *         required=false,
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Parameter(
 *         name="service_id",
 *         in="query",
 *         description="Filtro per ID del servizio",
 *         required=false,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Parameter(
 *         name="commission_name",
 *         in="query",
 *         description="Filtro per nome della commissione (case insensitive)",
 *         required=false,
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Parameter(
 *         name="commission_id",
 *         in="query",
 *         description="Filtro per ID della commissione",
 *         required=false,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(response=200, description="Successo"),
 *     @OA\Response(response=400, description="Errore"),
 *     @OA\Response(response=404, description="Filtro non riconosciuto")
 * )
 */
    public function index(Request $request)
{
    $validFilters = ['id', 'asset_name', 'service_name', 'service_id', 'commission_name', 'commission_id'];

    // controllo per filtri sconosciuti
    $unknownFilters = collect($request->all())->keys()->diff($validFilters)->toArray();

    if (!empty($unknownFilters)) {
        return response()->json(['message' => 'Filtro non riconosciuto'], 400);
    }

    $query = AssetClass::query();

    // filtri validi
    if ($request->has('id')) {
        $query->where('id', $request->input('id'));
    }

    if ($request->has('asset_name')) {
        $query->where('name', 'LIKE', '%' . $request->input('asset_name') . '%');
    }      

    if ($request->has('service_name')) {
        $query->whereHas('service', function($q) use ($request) {
            $q->where('name', 'LIKE', '%' . $request->input('service_name') . '%');
        });
    }

    if ($request->has('service_id')) {
        $query->where('service_id', $request->input('service_id'));
    }

    if ($request->has('commission_name')) {
        $query->whereHas('service', function($q) use ($request) {
            $q->whereHas('commission', function($cq) use ($request) {
                $cq->where('name', 'LIKE', '%' . $request->input('commission_name') . '%');
            });
        });
    }

    if ($request->has('commission_id')) {
        $query->whereHas('service', function($q) use ($request) {
            $q->where('commission_id', $request->input('commission_id'));
        });
    }

    $assetClasses = $query->with(['service', 'service.commission'])->get();

    $transformedAssetClasses = $assetClasses->map(function($assetClass) {
        return [
            'id' => $assetClass->id,
            'asset_name' => $assetClass->name,
            'service_name' => $assetClass->service ? $assetClass->service->name : null,
            'commission_name' => $assetClass->service && $assetClass->service->commission ? $assetClass->service->commission->name : null,
        ];
    });
    
    return response()->json($transformedAssetClasses);
}   

}
