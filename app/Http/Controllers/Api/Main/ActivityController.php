<?php

namespace App\Http\Controllers\Api\Main;

use App\Models\Activity;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ActivityResource;

class ActivityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $data = Activity::all();
            return new ActivityResource('Success', $data, 200);
        } catch (\Exception $e) {
            return new ActivityResource('Error', null, 500);
        } catch (\Throwable $th) {
            return new ActivityResource($th->getMessage(), null, 404);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'date' => 'nullable|date',
                'status' => 'nullable|in:active,inactive',
            ]);

            $activity = Activity::create([
                'name' => $request->name,
                'description' => $request->description,
                'date' => $request->date,
                'status' => $request->status ?? 'active',
            ]);

            return new ActivityResource('Activity created successfully', $activity, 201);
        } catch (\Exception $e) {
            return new ActivityResource($e->getMessage(), null, 500);
        } catch (\Throwable $th) {
            return new ActivityResource($th->getMessage(), null, 404);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $activity = Activity::findOrFail($id);
            return new ActivityResource('Success', $activity, 200);
        } catch (\Exception $e) {
            return new ActivityResource('Activity not found', null, 404);
        } catch (\Throwable $th) {
            return new ActivityResource($th->getMessage(), null, 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $activity = Activity::findOrFail($id);
            $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'date' => 'nullable|date',
                'status' => 'required|in:active,inactive',
            ]);

            $activity->update($request->all());

            return new ActivityResource('Activity updated successfully', $activity, 200);
        } catch (\Exception $e) {
            return new ActivityResource('Error updating activity', null, 500);
        } catch (\Throwable $th) {
            return new ActivityResource($th->getMessage(), null, 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $activity = Activity::findOrFail($id);
            $activity->delete();
            return new ActivityResource('Activity deleted successfully', null, 200);
        } catch (\Exception $e) {
            return new ActivityResource('Activity not found', null, 404);
        } catch (\Throwable $th) {
            return new ActivityResource($th->getMessage(), null, 500);
        }
    }
}
