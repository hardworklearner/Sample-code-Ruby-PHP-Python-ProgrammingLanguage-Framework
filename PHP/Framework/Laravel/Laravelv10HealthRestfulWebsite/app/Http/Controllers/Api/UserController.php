<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\User;
use App\Models\Exercise;
use App\Models\Achievement;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use App\Http\Requests\Api\UserRequest;
use App\Http\Requests\Api\UserUpdateRequest;
use App\Http\Requests\Api\UserCategoryRequest;
use App\Http\Requests\Api\UserExerciseRequest;

class UserController extends ApiController
{
    /**
     * Retrieve a collection of all users.
     */
    public function index()
    {
        return UserResource::collection(User::all());
    }

    /**
     * Create a new user.
     */
    public function store(UserRequest $request)
    {
        $user = User::create($request->validated());
        try {
            $user = User::create($request->validated());
            return new UserResource($user);
        } catch (Exception $ex) {
            return response()->json(["message" => "Create failed"], 500);
        }
    }

    /**
     * Display a specific user by ID.
     */
    public function show(User $user)
    {
        try {
            return new UserResource($user);
        } catch (Exception $ex) {
            return response()->json(["message" => "User not found"], 404);
        }
    }

    /**
     * Update an existing user.
     */
    public function update(UserUpdateRequest $request, User $user)
    {
        try {
            $user->update($request->validated());
            return new UserResource($user);
        } catch (Exception $ex) {
            return response()->json(["message" => "Update failed"], 500);
        }
    }

    /**
     * Delete a user by ID.
     */
    public function destroy(User $user)
    {
        try {
            $user->delete();
            return response()->json(["message" => "Delete successful"], 200);
        } catch (Exception $ex) {
            return response()->json(["message" => "Delete failed"], 500);
        }
    }

    /**
     * Add an achievement to the logged-in user.
     */
    public function achievement(Request $request, $achievement_id)
    {
        try {
            $achievement = Achievement::findOrFail($achievement_id);
            $user = $this->userLoggedIn();
            if ($user && $achievement) {
                $hasAchievement = $user->achievements()->where('achievement_id', $achievement_id)->exists();
                if ($hasAchievement) {
                    return response()->json(["message" => "Achievement already added"], 400);
                }

                $pivotData = ['complete_time' => now()];
                $user->achievements()->attach($achievement, $pivotData);
                return response()->json(["message" => "Achievement added", "data" => new UserResource($user)], 200);
            } else {
                return response()->json(["message" => "Achievement not found or User not found"], 404);
            }
        } catch (Exception $ex) {
            return response()->json(["message" => "Create failed: " . $ex->getMessage()], 500);
        }
    }

    /**
     * Retrieve achievements of the user logged in by Date or range of date
     */
    public function achievementsList(Request $request)
    {
        try {
            $user = $this->userLoggedIn();
            $achievements = $user->achievements();
            if ($request->has("date")) {
                $date = date('Y-m-d', strtotime($request->get("date")));
                $achievements = $achievements->whereDate('complete_time', $date)->get();

                return response()->json(["message" => "Achievements list", "data" => $achievements], 200);
            }

            if ($request->has("start_date")) {
                $start_date = date('Y-m-d', strtotime($request->get("start_date")));
                $achievements->whereDate('complete_time', '>=', $start_date);
            }
            if ($request->has("end_date")) {
                $end_date = date('Y-m-d', strtotime($request->get("end_date")));
                $achievements->whereDate('complete_time', '<=', $end_date);
            }
            $achievements = $achievements->get();
        } catch (Exception $ex) {
            return response()->json(["message" => "Achievement list failed: " . $ex->getMessage()], 404);
        }
        return response()->json(["message" => "Achievements list", 'data' => $achievements], 200);
    }


    /**
     * Retrieve weight records by date or range.
     */
    public function weightsList(Request $request)
    {
        try {
            $user = $this->userLoggedIn();
            $weights = $user->weights();
            if ($request->has("date")) {
                $date = date('Y-m-d', strtotime($request->get("date")));
                $weights = $weights->whereDate('calculate_time', $date)->get();

                return response()->json(["message" => "Bodies list", "data" => $weights], 200);
            }

            if ($request->has("start_date")) {
                $start_date = date('Y-m-d', strtotime($request->get("start_date")));
                $weights->whereDate('calculate_time', '>=', $start_date);
            }
            if ($request->has("end_date")) {
                $end_date = date('Y-m-d', strtotime($request->get("end_date")));
                $weights->whereDate('calculate_time', '<=', $end_date);
            }
            $weights = $weights->get();
        } catch (Exception $ex) {
            return response()->json(["message" => "Weight list failed: " . $ex->getMessage()], 404);
        }
        return response()->json(["message" => "Weights list", 'data' => $weights], 200);
    }

    /**
     * Retrieve body data by date or range.
     */
    public function BodiesList(Request $request)
    {
        try {
            $user = $this->userLoggedIn();
            $bodies = $user->bodies();
            if ($request->has("date")) {
                $date = date('Y-m-d', strtotime($request->get("date")));
                $bodies = $bodies->whereDate('calculate_time', $date)->get();

                return response()->json(["message" => "Bodies list", "data" => $bodies], 200);
            }

            if ($request->has("start_date")) {
                $start_date = date('Y-m-d', strtotime($request->get("start_date")));
                $bodies->whereDate('calculate_time', '>=', $start_date);
            }
            if ($request->has("end_date")) {
                $end_date = date('Y-m-d', strtotime($request->get("end_date")));
                $bodies->whereDate('calculate_time', '<=', $end_date);
            }
            $bodies = $bodies->get();
        } catch (Exception $ex) {
            return response()->json(["message" => "Bodies list failed: " . $ex->getMessage()], 404);
        }
        return response()->json(["message" => "Bodies list", 'data' => $bodies], 200);
    }

    /**
     * Summary of exercisesList
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function exercisesList(Request $request)
    {
        try {
            // Get the logged-in user.
            $user = $this->userLoggedIn();

            // Fetch the user's exercises as a query builder instance.
            $exercises = $user->exercises();

            // If a specific date is provided, filter exercises by that date.
            if ($request->has("date")) {
                $date = date('Y-m-d', strtotime($request->get("date")));
                $exercises = $exercises->whereDate('exercise_time', $date)->get();

                // Return the filtered exercises.
                return response()->json([
                    "message" => "Exercises list",
                    "data" => $exercises
                ], 200);
            }

            // If a start date is provided, filter exercises from that date onwards.
            if ($request->has("start_date")) {
                $start_date = date('Y-m-d', strtotime($request->get("start_date")));
                $exercises->whereDate('exercise_time', '>=', $start_date);
            }

            // If an end date is provided, filter exercises up to that date.
            if ($request->has("end_date")) {
                $end_date = date('Y-m-d', strtotime($request->get("end_date")));
                $exercises->whereDate('exercise_time', '<=', $end_date);
            }

            // Fetch the filtered exercises.
            $exercises = $exercises->get();
        } catch (Exception $ex) {
            // Handle any exceptions and return an error response.
            return response()->json([
                "message" => "Exercises list failed: " . $ex->getMessage()
            ], 404);
        }

        // Return the exercises list in the response.
        return response()->json([
            "message" => "Exercises list",
            'data' => $exercises
        ], 200);
    }

    /**
     * Add a new exercise for the user.
     */
    public function exercises(UserExerciseRequest $request)
    {
        try {
            $exercise_id = $request->exercise_id;
            $exercise = Exercise::findOrFail($exercise_id);
            $user = $this->userLoggedIn();
            if ($user && $exercise) {
                $hasExercise = $user->exercises()->where('exercise_id', $exercise_id)->exists();
                if ($hasExercise) {
                    return response()->json(["message" => "Exercise already added"], 400);
                }

                $pivotData = ['exercise_time' => now(), 'duration' => $request->input('duration'), 'calories_burned' => $request->calories_burned];
                $user->exercises()->attach($exercise, $pivotData);
                return response()->json(["message" => "Exercise added", "data" => new UserResource($user)], 200);
            } else {
                return response()->json(["message" => "Exercise not found or User not found"], 404);
            }
        } catch (Exception $ex) {
            return response()->json(["message" => "Create failed: " . $ex->getMessage()], 500);
        }
    }

    /**
     * Attach a category to the user.
     */
    public function categoryAdd(UserCategoryRequest $request)
    {
        try {
            $user = $this->userLoggedIn();
            $category_id = $request->category_id;
            $user->categories()->attach($category_id);
            return response()->json(["message" => "Category added", "data" => new UserResource($user)], 200);
        } catch (Exception $ex) {
            return response()->json(["message" => "Create failed: " . $ex->getMessage()], 500);
        }
    }

    /**
     * Summary of categoryRemove
     * @param \Illuminate\Http\Request $request
     * @param mixed $category_id
     * @return mixed
     */
    public function categoryRemove(Request $request, $category_id)
    {
        try {
            $user = $this->userLoggedIn();

            // Use `find` to avoid multiple queries and handle cases where category doesn't exist.
            $category = $user->categories()->find($category_id);

            if (!$category) {
                return response()->json([
                    "message" => "Category does not exist"
                ], 404);
            }

            // Detach the category from the user.
            $user->categories()->detach($category_id);

            return response()->json([
                "message" => "Category removed successfully",
                "data" => new UserResource($user)
            ], 200);
        } catch (Exception $ex) {
            // Use a more generic error message to avoid exposing sensitive details.
            return response()->json([
                "message" => "Failed to remove category"
            ], 500);
        }
    }


    /**
     * List all categories assigned to the user.
     */
    public function categoriesList(Request $request)
    {
        try {
            $user = $this->userLoggedIn();
            return response()->json(["message" => "Categories list", "data" => $user->categories], 200);
        } catch (Exception $ex) {
            return response()->json(
                ["message" => "Categories list failed: " . $ex->getMessage()],
                500
            );
        }
    }

    /**
     * List all posts of the user by date or range.
     */
    public function postsList(Request $request)
    {
        try {
            $user = $this->userLoggedIn();
            $posts = $user->posts();
            if ($request->has("date")) {
                $date = date('Y-m-d', strtotime($request->get("date")));
                $posts = $posts->whereDate('created_at', $date)->get();

                return response()->json(["message" => "Posts list", "data" => $posts], 200);
            }

            if ($request->has("start_date")) {
                $start_date = date('Y-m-d', strtotime($request->get("start_date")));
                $posts->whereDate('created_at', '>=', $start_date);
            }
            if ($request->has("end_date")) {
                $end_date = date('Y-m-d', strtotime($request->get("end_date")));
                $posts->whereDate('created_at', '<=', $end_date);
            }
            $posts = $posts->get();
        } catch (Exception $ex) {
            return response()->json(
                ["message" => "Posts list failed: " . $ex->getMessage()],
                500
            );
        }
        return response()->json(["message" => "Posts list", 'data' => $posts], 200);
    }

    /**
     * Summary of DiariesList
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function DiariesList(Request $request)
    {
        try {
            $user = $this->userLoggedIn();
            $diaries = $user->diaries();
            if ($request->has("date")) {
                $date = date('Y-m-d', strtotime($request->get("date")));
                $diaries = $diaries->whereDate('diary_time', $date)->get();

                return response()->json(["message" => "Posts list", "data" => $diaries], 200);
            }

            if ($request->has("start_date")) {
                $start_date = date('Y-m-d', strtotime($request->get("start_date")));
                $diaries->whereDate('diary_time', '>=', $start_date);
            }
            if ($request->has("end_date")) {
                $end_date = date('Y-m-d', strtotime($request->get("end_date")));
                $diaries->whereDate('diary_time', '<=', $end_date);
            }
            $diaries = $diaries->get();
        } catch (Exception $ex) {
            return response()->json(
                ["message" => "Diaries list failed: " . $ex->getMessage()],
                500
            );
        }
        return response()->json(["message" => "Diaries list", 'data' => $diaries], 200);
    }

    /**
     * Summary of mealsList
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function mealsList(Request $request)
    {
        try {
            $user = $this->userLoggedIn();
            $meals = $user->meals();
            if ($request->has("date")) {
                $date = date('Y-m-d', strtotime($request->get("date")));
                $meals = $meals->whereDate('meal_time', $date)->get();

                return response()->json(["message" => "Meals list", "data" => $meals], 200);
            }

            if ($request->has("start_date")) {
                $start_date = date('Y-m-d', strtotime($request->get("start_date")));
                $meals->whereDate('meal_time', '>=', $start_date);
            }
            if ($request->has("end_date")) {
                $end_date = date('Y-m-d', strtotime($request->get("end_date")));
                $meals->whereDate('meal_time', '<=', $end_date);
            }

            if ($request->has("category_id")) {
                $category_id = $request->get("category_id");
                $meals->where('category_id', $category_id);
            }

            if ($request->has("food_time")) {
                $food_time = $request->get("food_time");
                $meals->where('food_time', $food_time);
            }

            $meals = $meals->get();
        } catch (Exception $ex) {
            return response()->json(
                ["message" => "Meals list failed: " . $ex->getMessage()],
                500
            );
        }
        return response()->json(["message" => "Meals list", 'data' => $meals], 200);
    }
}
