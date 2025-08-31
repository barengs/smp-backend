# Lesson Hour API Documentation

## Overview

The Lesson Hour feature allows you to manage lesson schedules in the academic education module. This includes creating, reading, updating, and deleting lesson hour records.

## API Endpoints

### Get All Lesson Hours

```
GET /api/lesson-hour
```

Returns a list of all lesson hours ordered by their sequence.

**Response:**

```json
{
    "status": "success",
    "data": [
        {
            "id": 1,
            "name": "Jam Pelajaran 1",
            "start_time": "07:00:00",
            "end_time": "07:40:00",
            "order": 1,
            "description": "Jam pelajaran pertama pagi",
            "created_at": "2025-08-31T10:00:00.000000Z",
            "updated_at": "2025-08-31T10:00:00.000000Z"
        }
    ]
}
```

### Get Specific Lesson Hour

```
GET /api/lesson-hour/{id}
```

Returns a specific lesson hour by its ID.

**Response:**

```json
{
    "status": "success",
    "data": {
        "id": 1,
        "name": "Jam Pelajaran 1",
        "start_time": "07:00:00",
        "end_time": "07:40:00",
        "order": 1,
        "description": "Jam pelajaran pertama pagi",
        "created_at": "2025-08-31T10:00:00.000000Z",
        "updated_at": "2025-08-31T10:00:00.000000Z"
    }
}
```

### Create New Lesson Hour

```
POST /api/lesson-hour
```

Creates a new lesson hour record.

**Request Body:**

```json
{
    "name": "Jam Pelajaran 1",
    "start_time": "07:00:00",
    "end_time": "07:40:00",
    "order": 1,
    "description": "Jam pelajaran pertama pagi"
}
```

**Response:**

```json
{
    "status": "success",
    "message": "Lesson hour created successfully",
    "data": {
        "id": 1,
        "name": "Jam Pelajaran 1",
        "start_time": "07:00:00",
        "end_time": "07:40:00",
        "order": 1,
        "description": "Jam pelajaran pertama pagi",
        "created_at": "2025-08-31T10:00:00.000000Z",
        "updated_at": "2025-08-31T10:00:00.000000Z"
    }
}
```

### Update Lesson Hour

```
PUT /api/lesson-hour/{id}
```

Updates an existing lesson hour record.

**Request Body:**

```json
{
    "name": "Jam Pelajaran Updated",
    "start_time": "08:00:00",
    "end_time": "08:40:00",
    "order": 2,
    "description": "Jam pelajaran yang diperbarui"
}
```

**Response:**

```json
{
    "status": "success",
    "message": "Lesson hour updated successfully",
    "data": {
        "id": 1,
        "name": "Jam Pelajaran Updated",
        "start_time": "08:00:00",
        "end_time": "08:40:00",
        "order": 2,
        "description": "Jam pelajaran yang diperbarui",
        "created_at": "2025-08-31T10:00:00.000000Z",
        "updated_at": "2025-08-31T11:00:00.000000Z"
    }
}
```

### Delete Lesson Hour

```
DELETE /api/lesson-hour/{id}
```

Deletes a lesson hour record.

**Response:**

```json
{
    "status": "success",
    "message": "Lesson hour deleted successfully"
}
```

## Validation Rules

-   `name` (required, string, max:255) - Name of the lesson hour
-   `start_time` (required, time format H:i) - Start time of the lesson
-   `end_time` (required, time format H:i, after:start_time) - End time of the lesson (must be after start time)
-   `order` (optional, integer) - Sequence order of the lesson
-   `description` (optional, string) - Description of the lesson hour

## Error Responses

All error responses follow this format:

```json
{
    "status": "error",
    "message": "Error message",
    "error": "Detailed error information"
}
```

Validation errors return status code 422:

```json
{
    "status": "error",
    "message": "Validation failed",
    "errors": {
        "name": ["The name field is required."],
        "start_time": ["The start time field is required."],
        "end_time": [
            "The end time field is required.",
            "The end time must be after start time."
        ]
    }
}
```
