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
     *     @OA\Response(response=200, description="Creazione avvenuta con successo"),
     *     @OA\Response(response=400, description="Creazione fallita")
     * )
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $commission = Commission::create($validatedData);

        if ($commission) {
            return response()->json([
                'message' => 'Creazione avvenuta con successo',
                'commission' => $commission
            ], 200);
        } else {
            return response()->json(['error' => 'Creazione fallita'], 400);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/commissions/{id}",
     *     summary="Torna la commission con l'id specificato",
     *     tags={"Commissions"},
     *     @OA\Response(response=200, description="Successo"),
     *     @OA\Response(response=404, description="Commission non trovata")
     * )
     */
    public function show($id)
    {
        $commission = Commission::findOrFail($id);

        if ($commission) {
            return response()->json($commission, 200);
        } else {
            return response()->json(['error' => 'Commission non trovata'], 404);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/commissions/{id}",
     *     summary="Fa l'update di una commission con l'id specificato",
     *     tags={"Commissions"},
     *     @OA\Response(response=200, description="Update avvenuto con successo"),
     *     @OA\Response(response=400, description="Update fallito")
     * )
     */
    public function update(Request $request, $id)
    {
        $commission = Commission::findOrFail($id);

        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|nullable|string',
        ]);

        if ($commission->update($request->all())) {
            return response()->json($commission, 200);
        } else {
            return response()->json(['error' => 'Update fallito'], 400);
        }
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
