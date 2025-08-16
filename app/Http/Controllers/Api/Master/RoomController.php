<?php

namespace App\Http\Controllers\Api\Master;

use App\Http\Controllers\Controller;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * @group Master - Room
 *
 * API for managing rooms.
 */
class RoomController extends Controller
{
    /**
     * Get all rooms
     *
     * This endpoint is used to retrieve a paginated list of all rooms.
     *
     * @authenticated
     *
     * @response {
     *   "current_page": 1,
     *   "data": [
     *     {
     *       "id": 1,
     *       "name": "Room A",
     *       "hostel_id": 1,
     *       "capacity": 4,
     *       "description": "A nice room.",
     *       "is_active": true,
     *       "created_at": "2024-01-01T00:00:00.000000Z",
     *       "updated_at": "2024-01-01T00:00:00.000000Z",
     *       "hostel": {
     *         "id": 1,
     *         "name": "Hostel 1"
     *       }
     *     }
     *   ],
     *   "first_page_url": "http://localhost/api/master/room?page=1",
     *   "from": 1,
     *   "last_page": 1,
     *   "last_page_url": "http://localhost/api/master/room?page=1",
     *   "next_page_url": null,
     *   "path": "http://localhost/api/master/room",
     *   "per_page": 10,
     *   "prev_page_url": null,
     *   "to": 1,
     *   "total": 1
     * }
     */
    public function index()
    {
        $rooms = Room::with('hostel')->paginate(10);
        return response()->json($rooms);
    }

    /**
     * Create a new room
     *
     * This endpoint is used to create a new room.
     *
     * @authenticated
     *
     * @bodyParam name string required The name of the room. Example: Room B
     * @bodyParam hostel_id integer required The ID of the hostel. Example: 1
     * @bodyParam capacity integer required The capacity of the room. Example: 2
     * @bodyParam description string nullable A description of the room. Example: A small and cozy room.
     * @bodyParam is_active boolean nullable The status of the room. Example: true
     *
     * @response 201 {
     *   "id": 2,
     *   "name": "Room B",
     *   "hostel_id": 1,
     *   "capacity": 2,
     *   "description": "A small and cozy room.",
     *   "is_active": true,
     *   "created_at": "2024-01-01T00:00:00.000000Z",
     *   "updated_at": "2024-01-01T00:00:00.000000Z"
     * }
     *
     * @response 422 {
     *   "name": ["The name field is required."],
     *   "hostel_id": ["The hostel id field is required."]
     * }
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'hostel_id' => 'required|exists:hostels,id',
            'capacity' => 'required|integer',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $room = Room::create($request->all());

        return response()->json($room, 201);
    }

    /**
     * Get a specific room
     *
     * This endpoint is used to retrieve the details of a specific room.
     *
     * @authenticated
     *
     * @urlParam id integer required The ID of the room.
     *
     * @response {
     *   "id": 1,
     *   "name": "Room A",
     *   "hostel_id": 1,
     *   "capacity": 4,
     *   "description": "A nice room.",
     *   "is_active": true,
     *   "created_at": "2024-01-01T00:00:00.000000Z",
     *   "updated_at": "2024-01-01T00:00:00.000000Z",
     *   "hostel": {
     *     "id": 1,
     *     "name": "Hostel 1"
     *   }
     * }
     *
     * @response 404 {
     *   "message": "Room not found"
     * }
     */
    public function show($id)
    {
        try {
            $room = Room::with('hostel')->findOrFail($id);
            return response()->json($room);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Room not found'], 404);
        }
    }

    /**
     * Update a room
     *
     * This endpoint is used to update the details of a specific room.
     *
     * @authenticated
     *
     * @urlParam id integer required The ID of the room.
     *
     * @bodyParam name string required The name of the room. Example: Room C
     * @bodyParam hostel_id integer required The ID of the hostel. Example: 1
     * @bodyParam capacity integer required The capacity of the room. Example: 3
     * @bodyParam description string nullable A description of the room. Example: An updated room.
     * @bodyParam is_active boolean nullable The status of the room. Example: false
     *
     * @response {
     *   "id": 1,
     *   "name": "Room C",
     *   "hostel_id": 1,
     *   "capacity": 3,
     *   "description": "An updated room.",
     *   "is_active": false,
     *   "created_at": "2024-01-01T00:00:00.000000Z",
     *   "updated_at": "2024-01-01T00:00:00.000000Z"
     * }
     *
     * @response 404 {
     *   "message": "Room not found"
     * }
     *
     * @response 422 {
     *   "name": ["The name field is required."]
     * }
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'hostel_id' => 'required|exists:hostels,id',
            'capacity' => 'required|integer',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        try {
            $room = Room::findOrFail($id);
            $room->update($request->all());
            return response()->json($room);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Room not found'], 404);
        }
    }

    /**
     * Delete a room
     *
     * This endpoint is used to delete a specific room.
     *
     * @authenticated
     *
     * @urlParam id integer required The ID of the room.
     *
     * @response 204
     *
     * @response 404 {
     *   "message": "Room not found"
     * }
     */
    public function destroy($id)
    {
        try {
            $room = Room::findOrFail($id);
            $room->delete();
            return response()->json(null, 204);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Room not found'], 404);
        }
    }
}
