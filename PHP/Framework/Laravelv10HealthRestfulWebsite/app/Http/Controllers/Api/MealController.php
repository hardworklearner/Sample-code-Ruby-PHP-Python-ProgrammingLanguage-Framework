<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\Meal;
use App\Http\Requests\Api\MealRequest;
use App\Http\Resources\MealResource;
use Illuminate\Support\Facades\Storage;

class MealController extends ApiController
{
    public function index()
    {
        return MealResource::collection(Meal::all());
    }

    public function store(MealRequest $request)
    {
        try {
            $meal_data = $request->validated();
            if ($request->file("picture") != null) {
                $path = $request->file('picture')->store('images/meals');
                $meal_data['picture'] = $path;
            }
            $meal_data['user_id'] = auth()->user()->id;
            $meal = Meal::create($meal_data);
            return new MealResource($meal);
        } catch (Exception $ex) {
            return response()->json(["message" => "Create failed: " . $ex->getMessage()], 500);
        }
    }

    public function show(Meal $meal)
    {

        try {
            return new MealResource($meal);
        } catch (Exception $ex) {
            return response()->json(["message" => "Meal not found: " . $ex->getMessage()], 404);
        }
    }

    public function update(MealRequest $request, Meal $meal)
    {
        try {
            $meal_data = $request->validated();
            if ($request->file("picture") != null) {
                $path = $request->file('picture')->store('images/meals');
                $meal_data['picture'] = $path;
                if ($path) {
                    Storage::delete($meal->picture);
                }
            } else {
                unset($meal_data['picture']);
            }
            $meal_data['user_id'] = auth()->user()->id;
            $meal->update($meal_data);
            return new MealResource($meal);
        } catch (Exception $ex) {
            return response()->json(["message" => "Update failed: " . $ex->getMessage()], 500);
        }
    }

    public function destroy(Meal $meal)
    {
        try {
            if (Storage::exists($meal->picture)) {
                Storage::delete($meal->picture);
            }
            $meal->delete();
            return response()->json(["message" => "Delete successful"], 200);
        } catch (Exception $ex) {
            return response()->json(["message" => "Delete failed: " . $ex->getMessage()], 500);
        }
    }
}
