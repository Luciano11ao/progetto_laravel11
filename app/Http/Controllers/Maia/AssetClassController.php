<?php

namespace App\Http\Controllers\Maia;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AssetClass;
use App\Models\Commission;
use App\Models\Service;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class AssetClassController extends Controller
{
    public function index()
    {
        $assetClasses = AssetClass::all();
        return response()->json($assetClasses);
    }

    public function store(Request $request)
{
    Log::debug('Entering store method for AssetClass');

    try {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'service_id' => 'required|integer|exists:services,id',
            'commission_id' => 'required|integer|exists:commissions,id',
        ]);
    } catch (ValidationException $e) {
        Log::error('Validation failed for AssetClass: ' . json_encode($e->errors()));
        return response()->json([
            'message' => 'Validation failed',
            'errors' => $e->errors()
        ], 422);
    }

    Log::debug('Validation passed for AssetClass: ' . json_encode($validatedData));

    try {
        $assetClass = AssetClass::create($validatedData);
        Log::debug('Asset class created successfully: ' . json_encode($assetClass));

        return response()->json([
            'message' => 'Asset class created successfully',
            'data' => $assetClass
        ], 201);
    } catch (\Exception $e) {
        Log::error('Error creating asset class: ' . $e->getMessage());
        return response()->json([
            'message' => 'Failed to create asset class',
            'error' => $e->getMessage()
        ], 500);
    }

}

public function show($id)
{
    $assetClass = AssetClass::find($id);
    if ($assetClass) {
        return response()->json($assetClass);
    } else {
        return response()->json(['message' => 'Asset class not found'], 404);
    }
}

    public function update(Request $request, $id)
{
    $validatedData = $request->validate([
        'name' => 'sometimes|required|string|max:255',
        'service_id' => 'sometimes|required|integer|exists:services,id',
        'commission_id' => 'sometimes|required|integer|exists:commissions,id',
    ]);

    $assetClass = AssetClass::find($id);
    if ($assetClass) {
        $assetClass->update($validatedData);
        return response()->json([
            'message' => 'Asset class updated successfully',
            'data' => $assetClass
        ]);
    } else {
        return response()->json(['message' => 'Asset class not found'], 404);
    }
}

    public function destroy($id)
{
    $assetClass = AssetClass::find($id);
    if ($assetClass) {
        $assetClass->delete();
        return response()->json(['message' => 'Asset class deleted successfully']);
    } else {
        return response()->json(['message' => 'Asset class not found'], 404);
    }
}

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
