<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\Weight;
use App\Http\Requests\Api\WeightRequest;
use App\Http\Resources\WeightResource;

class WeightController extends ApiController
{
    public function index()
    {
        return WeightResource::collection(Weight::all());
    }

    public function store(WeightRequest $request)
    {
        try {
            $weight_data = $request->validated();
            $weight_data['user_id'] = auth()->user()->id;
            $weight = Weight::create($weight_data);
            return new WeightResource($weight);
        } catch (Exception $ex) {
            return response()->json(["message" => "Create failed: " . $ex->getMessage()], 500);
        }
    }

    public function show(Weight $weight)
    {

        try {
            if (!$this->isAdminAuthorization() && $weight->user_id != auth()->user()->id) {
                return response()->json(["message" => "Unauthorized"], 401);
            };
            return new WeightResource($weight);
        } catch (Exception $ex) {
            return response()->json(["message" => "Weight not found: " . $ex->getMessage()], 404);
        }
    }

    public function update(WeightRequest $request, Weight $weight)
    {
        try {
            if (!$this->isAdminAuthorization() && $weight->user_id != auth()->user()->id) {
                return response()->json(["message" => "Unauthorized"], 401);
            };
            $weight->update($request->validated());
            return new WeightResource($weight);
        } catch (Exception $ex) {
            return response()->json(["message" => "Update failed: " . $ex->getMessage()], 500);
        }
    }

    public function destroy(Weight $weight)
    {
        try {
            if (!$this->isAdminAuthorization() && $weight->user_id != auth()->user()->id) {
                return response()->json(["message" => "Unauthorized"], 401);
            };
            $weight->delete();
            return response()->json(["message" => "Delete successful"], 200);
        } catch (Exception $ex) {
            return response()->json(["message" => "Delete failed: " . $ex->getMessage()], 500);
        }
    }
}
