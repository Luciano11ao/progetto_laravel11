<?php

namespace App\Http\Controllers\Maia;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Commission;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class CommissionController extends Controller
{
    public function index()
    {
        $commissions = Commission::all();
        return response()->json($commissions);
    }

    public function store(Request $request)
{
    Log::debug('Entering store method for Commission');

    try {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);
    } catch (ValidationException $e) {
        Log::error('Validation failed for Commission: ' . json_encode($e->errors()));
        return response()->json([
            'message' => 'Validation failed',
            'errors' => $e->errors()
        ], 422);
    }

    Log::debug('Validation passed for Commission: ' . json_encode($validatedData));

    try {
        $commission = Commission::create($validatedData);
        Log::debug('Commission created successfully: ' . json_encode($commission));

        return response()->json($commission, 201);
    } catch (\Exception $e) {
        Log::error('Error creating commission: ' . $e->getMessage());
        return response()->json([
            'message' => 'Failed to create commission',
            'error' => $e->getMessage()
        ], 500);
    }
}

    public function show($id)
    {
        $commission = Commission::findOrFail($id);
        return response()->json($commission);
    }

    public function update(Request $request, $id)
    {
        $commission = Commission::findOrFail($id);

        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|nullable|string',
        ]);

        $commission->update($request->all());
        return response()->json($commission);
    }

    public function destroy($id)
    {
    $commission = Commission::find($id);
    if ($commission) {
        $commission->delete();
        return response()->json(['message' => 'commission deleted successfully']);
    } else {
        return response()->json(['message' => 'commission not found'], 404);
    }
}
}
