<?php

namespace App\Http\Controllers\Maia;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Service;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::all();
        return response()->json($services);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'commission_id' => 'required|exists:commissions,id',
        ]);

        $service = Service::create($request->all());
        return response()->json($service, 201);
    }

    public function show($id)
    {
        $service = Service::findOrFail($id);
        return response()->json($service);
    }

    public function update(Request $request, $id)
    {
        $service = Service::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'commission_id' => 'required|exists:commissions,id',
        ]);

        $service->update($request->all());
        return response()->json($service);
    }

    public function destroy($id)
    {
    $service = Service::find($id);
    if ($service) {
        $service->delete();
        return response()->json(['message' => 'service deleted successfully']);
    } else {
        return response()->json(['message' => 'service not found'], 404);
    }
}
}
