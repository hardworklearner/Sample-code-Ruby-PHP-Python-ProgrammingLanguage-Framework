<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\Exercise;
use App\Http\Requests\Api\ExerciseRequest;
use App\Http\Resources\ExerciseResource;
use Illuminate\Support\Facades\Storage;

class ExerciseController extends ApiController
{
    public function index()
    {
        return ExerciseResource::collection(Exercise::all());
    }

    public function store(ExerciseRequest $request)
    {
        try {
            $exercise_data = $request->validated();
            if ($request->hasFile('exercise_image') == true) {
                $path = $request->file('exercise_image')->store('images/exercises');
                $exercise_data['exercise_image'] = $path;
            } else {
                $exercise_data['exercise_image'] = '';
                // unset($exercise_data['exercise_image']);
            }
            $exercise = Exercise::create($exercise_data);
            return new ExerciseResource($exercise);
        } catch (Exception $ex) {
            return response()->json(["message" => "Create failed: " . $ex->getMessage()], 500);
        }
    }

    public function show(Exercise $exercise)
    {

        try {
            return new ExerciseResource($exercise);
        } catch (Exception $ex) {
            return response()->json(["message" => "Exercise not found: " . $ex->getMessage()], 404);
        }
    }

    public function update(ExerciseRequest $request, Exercise $exercise)
    {
        try {
            $exercise_data = $request->validated();
            $path = $request->file('exercise_image')->store('images/exercises');
            if ($request->hasFile('exercise_image') == true) {
                $path = $request->file('exercise_image')->store('images/exercises');
                $exercise_data['exercise_image'] = $path;
                if ($path) {
                    Storage::delete($exercise->exercise_image);
                }
            } else {
                unset($exercise_data['exercise_image']);
            }
            $exercise->update($exercise_data);
            return new ExerciseResource($exercise);
        } catch (Exception $ex) {
            return response()->json(["message" => "Update failed: " . $ex->getMessage()], 500);
        }
    }

    public function destroy(Exercise $exercise)
    {
        try {
            if (Storage::exists($exercise->exercise_image)) {
                Storage::delete($exercise->exercise_image);
            }
            $exercise->delete();
            return response()->json(["message" => "Delete successful"], 200);
        } catch (Exception $ex) {
            return response()->json(["message" => "Delete failed: " . $ex->getMessage()], 500);
        }
    }
}
