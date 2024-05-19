<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\Diary;
use App\Http\Requests\Api\DiaryRequest;
use App\Http\Resources\DiaryResource;

class DiaryController extends ApiController
{
    public function index()
    {
        return DiaryResource::collection(Diary::all());
    }

    public function store(DiaryRequest $request)
    {
        try {
            $diary_data = $request->validated();
            $diary_data['user_id'] = auth()->user()->id;
            $diary = Diary::create($diary_data);
            return new DiaryResource($diary);
        } catch (Exception $ex) {
            return response()->json(["message" => "Create failed: " . $ex->getMessage()], 500);
        }
    }

    public function show(Diary $diary)
    {

        try {
            if (!$this->isAdminAuthorization() && $diary->user_id != auth()->user()->id) {
                return response()->json(["message" => "Unauthorized"], 401);
            };
            return new DiaryResource($diary);
        } catch (Exception $ex) {
            return response()->json(["message" => "Diary not found: " . $ex->getMessage()], 404);
        }
    }

    public function update(DiaryRequest $request, Diary $diary)
    {
        try {
            if (!$this->isAdminAuthorization() && $diary->user_id != auth()->user()->id) {
                return response()->json(["message" => "Unauthorized"], 401);
            };
            $diary->update($request->validated());
            return new DiaryResource($diary);
        } catch (Exception $ex) {
            return response()->json(["message" => "Update failed: " . $ex->getMessage()], 500);
        }
    }

    public function destroy(Diary $diary)
    {
        try {
            if (!$this->isAdminAuthorization() && $diary->user_id != auth()->user()->id) {
                return response()->json(["message" => "Unauthorized"], 401);
            };
            $diary->delete();
            return response()->json(["message" => "Delete successful"], 200);
        } catch (Exception $ex) {
            return response()->json(["message" => "Delete failed: " . $ex->getMessage()], 500);
        }
    }
}
