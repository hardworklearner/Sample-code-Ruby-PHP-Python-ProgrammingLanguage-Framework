<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\Achievement;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\AchievementResource;
use App\Http\Requests\Api\AchievementRequest;

class AchievementController extends Controller
{
    public function index()
    {
        return AchievementResource::collection(Achievement::all());
    }

    public function store(AchievementRequest $request)
    {
        try {
            $achievement = Achievement::create($request->validated());
            return new AchievementResource($achievement);
        } catch (Exception $ex) {
            return response()->json(["message" => "Create failed: " . $ex->getMessage()], 500);
        }
    }

    public function show(Achievement $achievement)
    {
        try {
            return new AchievementResource($achievement);
        } catch (Exception $ex) {
            return response()->json(["message" => "Achievement not found: " . $ex->getMessage()], 404);
        }
    }

    public function update(AchievementRequest $request, Achievement $achievement)
    {
        try {
            $achievement->update($request->validated());
            return new AchievementResource($achievement);
        } catch (Exception $ex) {
            return response()->json(["message" => "Update failed: " . $ex->getMessage()], 500);
        }
    }

    public function destroy(Achievement $achievement)
    {
        try {
            $achievement->delete();
            return response()->json(["message" => "Delete successful"], 200);
        } catch (Exception $ex) {
            return response()->json(["message" => "Delete failed: " . $ex->getMessage()], 500);
        }
    }
}
