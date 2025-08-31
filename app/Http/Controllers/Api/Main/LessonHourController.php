<?php

namespace App\Http\Controllers\Api\Main;

use App\Http\Controllers\Controller;
use App\Models\LessonHour;
use App\Http\Resources\LessonHourResource;
use App\Http\Requests\LessonHourRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LessonHourController extends Controller
{
    /**
     * Display a listing of the lesson hours.
     */
    public function index()
    {
        try {
            $lessonHours = LessonHour::orderBy('order')->get();
            return response()->json([
                'status' => 'success',
                'data' => LessonHourResource::collection($lessonHours)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve lesson hours',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created lesson hour in storage.
     */
    public function store(LessonHourRequest $request)
    {
        try {
            DB::beginTransaction();

            $lessonHour = LessonHour::create($request->all());

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Lesson hour created successfully',
                'data' => new LessonHourResource($lessonHour)
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create lesson hour',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified lesson hour.
     */
    public function show(LessonHour $lessonHour)
    {
        try {
            return response()->json([
                'status' => 'success',
                'data' => new LessonHourResource($lessonHour)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve lesson hour',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified lesson hour in storage.
     */
    public function update(LessonHourRequest $request, LessonHour $lessonHour)
    {
        try {
            DB::beginTransaction();

            $lessonHour->update($request->all());

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Lesson hour updated successfully',
                'data' => new LessonHourResource($lessonHour)
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update lesson hour',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified lesson hour from storage.
     */
    public function destroy(LessonHour $lessonHour)
    {
        try {
            DB::beginTransaction();

            $lessonHour->delete();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Lesson hour deleted successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete lesson hour',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
