<?php

namespace App\Http\Controllers\Maia;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Commission;
use Illuminate\Validation\ValidationException;
use OpenApi\Attributes as OA;

class CommissionController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/commissions",
     *     summary="Torna la lista delle commission",
     *     tags={"Commissions"},
     *     @OA\Response(response=200, description="Successo"),
     *     @OA\Response(response=400, description="Errore")
     * )
     */
    public function index()
    {
        $commissions = Commission::all();
        return response()->json($commissions);
    }

    /**
     * @OA\Post(
     *     path="/api/commissions",
     *     summary="Crea una commission",
     *     tags={"Commissions"},
     *     @OA\Response(response=200, description="Commission creata con successo"),
     *     @OA\Response(response=400, description="Commission già esistente")
     * )
     */
    public function store(Request $request)
    {
        // Validazione
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        // Verifica del nome
        $existingCommission = Commission::where('name', $validatedData['name'])->first();
        if ($existingCommission) {
            return response()->json(['message' => 'Commission già esistente'], 400);
        }

        $commission = Commission::create($validatedData);

        return response()->json([
            'message' => 'Commission creata con successo',
            'data' => $commission
        ], 200);
    }

    /**
     * @OA\Put(
     *     path="/api/commissions/{id}",
     *     summary="Fa l'update di una commission con l'id specificato",
     *     tags={"Commissions"},
     *     @OA\Response(response=200, description="Update avvenuto con successo"),
     *     @OA\Response(response=400, description="Commission già esistente"),
     *     @OA\Response(response=404, description="Commission inesistente")
     * )
     */
    public function update(Request $request, $id)
    {
        // Validazione
        $validatedData = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|nullable|string',
        ]);

        // Verifica del nome
        $existingCommission = Commission::where('name', $validatedData['name'])->first();
        if ($existingCommission) {
            return response()->json(['message' => 'Commission già esistente'], 400);
        }

        // Controllo dell'id
        $commission = Commission::find($id);
        if (!$commission) {
            return response()->json(['message' => 'Commission inesistente'], 404);
        }

        $commission->update($validatedData);

        return response()->json([
            'message' => 'Update avvenuto con successo',
            'data' => $commission
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/commissions/{id}",
     *     summary="Elimina la commission con l'id specificato",
     *     tags={"Commissions"},
     *     @OA\Response(response=200, description="Commission eliminata con successo"),
     *     @OA\Response(response=404, description="Commission non trovata")
     * )
     */
    public function destroy($id)
    {
        $commission = Commission::find($id);
        if ($commission) {
            $commission->delete();
            return response()->json(['message' => 'Commission eliminata con successo']);
        } else {
            return response()->json(['message' => 'Commission non trovata'], 404);
        }
    }
}
