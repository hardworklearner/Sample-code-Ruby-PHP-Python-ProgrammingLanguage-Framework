<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\Post;
// use Illuminate\Http\Client\Request;
use App\Http\Resources\PostResource;
use App\Http\Requests\Api\PostRequest;
use Illuminate\Http\Request; // Correct class for handling incoming requests

class PostController extends ApiController
{
    public function index(Request $request)
    {
        try {
            $posts = Post::query();
            if ($request->has("date")) {
                $date = date('Y-m-d', strtotime($request->get("date")));
                $posts = $posts->whereDate('created_at', $date)->get();

                return response()->json(["message" => "Posts list", 'data' => PostResource::collection($posts)], 200);
            }
            if ($request->has("start_date")) {
                $start_date = date('Y-m-d', strtotime($request->get("start_date")));
                $posts->whereDate('created_at', '>=', $start_date);
            }
            if ($request->has("end_date")) {
                $end_date = date('Y-m-d', strtotime($request->get("end_date")));
                $posts->whereDate('created_at', '<=', $end_date);
            }

            if ($request->has("user_id")) {
                $posts->where('user_id', $request->get("user_id"));
            }

            if ($request->has("category_id")) {
                $posts->where('category_id', $request->get("category_id"));
            }

            if ($request->has("search")) {
                $search = $request->get("search");
                $posts->where(function ($query) use ($search) {
                    $query->where('title', 'LIKE', "%{$search}%")
                        ->orWhere('post_content', 'LIKE', "%{$search}%")
                        ->orWhere('description', 'LIKE', "%{$search}%");
                });
            }

            $posts = $posts->get();
        } catch (Exception $ex) {
            return response()->json(
                ["message" => "Posts list failed: " . $ex->getMessage()],
                500
            );
        }
        return response()->json(["message" => "Posts list", 'data' => PostResource::collection($posts)], 200);
    }

    public function store(PostRequest $request)
    {
        try {
            $post_data = $request->validated();
            $post_data['user_id'] = auth()->user()->id;
            $post = Post::create($post_data);
            return new PostResource($post);
        } catch (Exception $ex) {
            return response()->json(["message" => "Create failed: " . $ex->getMessage()], 500);
        }
    }

    public function show(Post $post)
    {

        try {
            if (!$this->isAdminAuthorization() && $post->user_id != auth()->user()->id) {
                return response()->json(["message" => "Unauthorized"], 401);
            };
            return new PostResource($post);
        } catch (Exception $ex) {
            return response()->json(["message" => "Post not found: " . $ex->getMessage()], 404);
        }
    }

    public function update(PostRequest $request, Post $post)
    {
        try {
            if (!$this->isAdminAuthorization() && $post->user_id != auth()->user()->id) {
                return response()->json(["message" => "Unauthorized"], 401);
            };
            $post->update($request->validated());
            return new PostResource($post);
        } catch (Exception $ex) {
            return response()->json(["message" => "Update failed: " . $ex->getMessage()], 500);
        }
    }

    public function destroy(Post $post)
    {
        try {
            if (!$this->isAdminAuthorization() && $post->user_id != auth()->user()->id) {
                return response()->json(["message" => "Unauthorized"], 401);
            };
            $post->delete();
            return response()->json(["message" => "Delete successful"], 200);
        } catch (Exception $ex) {
            return response()->json(["message" => "Delete failed: " . $ex->getMessage()], 500);
        }
    }
}
