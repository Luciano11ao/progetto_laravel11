<?php

namespace App\Http\Controllers\Maia;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Service;

class ServiceController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/services",
     *     summary="Torna una lista di service",
     *     tags={"Service"},
     *     @OA\Response(response=200, description="Successo"),
     *     @OA\Response(response=400, description="Errore")
     * )
     */
    public function index()
    {
        $services = Service::all();
        return response()->json($services);
    }

    /**
     * @OA\Post(
     *     path="/api/services",
     *     summary="Crea un service",
     *     tags={"Service"},
     *     @OA\Response(response=200, description="Service creato con successo"),
     *     @OA\Response(response=400, description="Service già esistente")
     * )
     */
    public function store(Request $request)
    {
        // Validazione
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'commission_id' => 'required|exists:commissions,id',
        ]);

        // Verifica del nome
        $existingService = Service::where('name', $validatedData['name'])->first();
        if ($existingService) {
            return response()->json(['message' => 'Service già esistente'], 400);
        }

        $service = Service::create($validatedData);

        return response()->json([
            'message' => 'Service creato con successo',
            'data' => $service
        ], 200);
    }

    /**
     * @OA\Put(
     *     path="/api/services/{id}",
     *     summary="Fa l'update del service con l'id specificato",
     *     tags={"Service"},
     *     @OA\Response(response=200, description="Update avvenuto con successo"),
     *     @OA\Response(response=404, description="Service inesistente"),
     *     @OA\Response(response=400, description="Service già esistente")
     * )
     */
    public function update(Request $request, $id)
    {
        // Validazione
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'commission_id' => 'required|exists:commissions,id',
        ]);

        // Verifica del nome
        $existingService = Service::where('name', $validatedData['name'])->first();
        if ($existingService) {
            return response()->json(['message' => 'Service già esistente'], 400);
        }

        // Controllo dell'id
        $service = Service::find($id);
        if (!$service) {
            return response()->json(['message' => 'Service inesistente'], 404);
        }

        $service->update($validatedData);

        return response()->json([
            'message' => 'Update avvenuto con successo',
            'data' => $service
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/services/{id}",
     *     summary="Elimina il service con l'id specificato",
     *     tags={"Service"},
     *     @OA\Response(response=200, description="Service eliminato con successo"),
     *     @OA\Response(response=404, description="Service non trovato")
     * )
     */
    public function destroy($id)
    {
        $service = Service::find($id);
        if ($service) {
            $service->delete();
            return response()->json(['message' => 'Service eliminato con successo']);
        } else {
            return response()->json(['message' => 'Service non trovato'], 404);
        }
    }
}
