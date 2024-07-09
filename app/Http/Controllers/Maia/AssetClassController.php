<?php

namespace App\Http\Controllers\Maia;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AssetClass;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

/**
 * @OA\Info(title="Documentazione API rest", version="1.0")
 */

class AssetClassController extends Controller
{

    /**
     * @OA\Get(
     *     path="/api/asset_classes",
     *     summary="Torna la lista degli asset classes",
     *     tags={"AssetClass"},
     *     @OA\Response(response=200, description="Successo"),
     *     @OA\Response(response=400, description="Errore")
     * )
     */
    public function index()
    {
        $assetClasses = AssetClass::all();
        return response()->json($assetClasses);
    }

    /**
     * @OA\Post(
     *     path="/api/asset_classes",
     *     summary="Crea un'asset class",
     *     tags={"AssetClass"},
     *     @OA\Response(response=200, description="Asset class creata con successo"),
     *     @OA\Response(response=400, description="Errore 400!")
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

        // Creazione di una nuova istanza di AssetClass
        $assetClass = AssetClass::create($validatedData);

        // Blocco if per la risposta
        if ($assetClass) {
            return response()->json([
                'message' => 'Asset class creata con successo',
                'data' => $assetClass
            ], 200);
        } else {
            return response()->json([
                'message' => 'Errore 400!'
            ], 400);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/asset_classes/{id}",
     *     summary="Torna l'asset class con l'id specificato nell'URL",
     *     tags={"AssetClass"},
     *     @OA\Response(response=200, description="Successo"),
     *     @OA\Response(response=404, description="Asset class non trovata")
     * )
     */
    public function show($id)
    {
        $assetClass = AssetClass::find($id);
        if ($assetClass) {
            return response()->json($assetClass);
        } else {
            return response()->json(['message' => 'Asset class non trovata'], 404);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/asset_classes/{id}",
     *     summary="Aggiorna un'asset class con l'id specificato nell'URL",
     *     tags={"AssetClass"},
     *     @OA\Response(response=200, description="Update avvenuto con successo"),
     *     @OA\Response(response=404, description="Asset class non trovata")
     * )
     */
    public function update(Request $request, $id)
    {
        // Validazione
        $validatedData = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'service_id' => 'sometimes|required|integer|exists:services,id',
            'commission_id' => 'sometimes|required|integer|exists:commissions,id',
        ]);

        $assetClass = AssetClass::find($id);
        if ($assetClass) {
            $assetClass->update($validatedData);
            return response()->json([
                'message' => 'Update avvenuto con successo',
                'data' => $assetClass
            ]);
        } else {
            return response()->json(['message' => 'Asset class non trovata'], 404);
        }
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
     *     @OA\Response(response=200, description="Successo"),
     *     @OA\Response(response=400, description="Errore")
     * )
     */
    public function getAssetClasses(Request $request)
    {
        $query = AssetClass::query();

        if ($request->has('id')) {
            $query->where('id', $request->input('id'));
        }

        if ($request->has('name')) {
            $query->where('name', 'like', '%' . $request->input('name') . '%');
        }

        if ($request->has('service_id')) {
            $query->where('service_id', $request->input('service_id'));
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
