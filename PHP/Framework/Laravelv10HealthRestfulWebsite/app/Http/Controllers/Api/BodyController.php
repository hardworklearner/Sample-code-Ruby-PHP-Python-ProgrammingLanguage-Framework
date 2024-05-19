<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\Body;
use App\Http\Requests\Api\BodyRequest;
use App\Http\Resources\BodyResource;

class BodyController extends ApiController
{
    public function index()
    {
        return BodyResource::collection(Body::all());
    }

    public function store(BodyRequest $request)
    {
        try {
            $body_data = $request->validated();
            $body_data['user_id'] = auth()->user()->id;
            $body = Body::create($body_data);
            return new BodyResource($body);
        } catch (Exception $ex) {
            return response()->json(["message" => "Create failed: " . $ex->getMessage()], 500);
        }
    }

    public function show(Body $body)
    {

        try {
            if (!$this->isAdminAuthorization() && $body->user_id != auth()->user()->id) {
                return response()->json(["message" => "Unauthorized"], 401);
            };
            return new BodyResource($body);
        } catch (Exception $ex) {
            return response()->json(["message" => "Body not found: " . $ex->getMessage()], 404);
        }
    }

    public function update(BodyRequest $request, Body $body)
    {
        try {
            if (!$this->isAdminAuthorization() && $body->user_id != auth()->user()->id) {
                return response()->json(["message" => "Unauthorized"], 401);
            };
            $body->update($request->validated());
            return new BodyResource($body);
        } catch (Exception $ex) {
            return response()->json(["message" => "Update failed: " . $ex->getMessage()], 500);
        }
    }

    public function destroy(Body $body)
    {
        try {
            if (!$this->isAdminAuthorization() && $body->user_id != auth()->user()->id) {
                return response()->json(["message" => "Unauthorized"], 401);
            };
            $body->delete();
            return response()->json(["message" => "Delete successful"], 200);
        } catch (Exception $ex) {
            return response()->json(["message" => "Delete failed: " . $ex->getMessage()], 500);
        }
    }
}
