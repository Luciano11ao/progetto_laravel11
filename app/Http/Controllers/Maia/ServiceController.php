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
     *     @OA\Response(response=400, description="Creazione service fallita")
     * )
     */
    public function store(Request $request)
    {
        // Validazione
        $request->validate([
            'name' => 'required|string|max:255',
            'commission_id' => 'required|exists:commissions,id',
        ]);

        $service = Service::create($request->all());

        // Blocco if per la risposta
        if ($service) {
            return response()->json($service, 200);
        } else {
            return response()->json(['error' => 'Creazione service fallita'], 400);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/services/{id}",
     *     summary="Torna il service con l'id specificato",
     *     tags={"Service"},
     *     @OA\Response(response=200, description="Successo"),
     *     @OA\Response(response=404, description="Service non trovato")
     * )
     */
    public function show($id)
    {
        $service = Service::findOrFail($id);
        return response()->json($service);
    }

    /**
     * @OA\Put(
     *     path="/api/services/{id}",
     *     summary="Fa l'update del service con l'id specificato",
     *     tags={"Service"},
     *     @OA\Response(response=200, description="Update avvenuto con successo"),
     *     @OA\Response(response=404, description="Service non trovato")
     * )
     */
    public function update(Request $request, $id)
    {
        $service = Service::findOrFail($id);

        // Validazione
        $request->validate([
            'name' => 'required|string|max:255',
            'commission_id' => 'required|exists:commissions,id',
        ]);

        if ($service->update($request->all())) {
            return response()->json($service, 200);
        } else {
            return response()->json(['error' => 'Service non trovato'], 404);
        }
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
