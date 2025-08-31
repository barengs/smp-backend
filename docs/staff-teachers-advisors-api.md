# Staff Teachers and Advisors API Documentation

## Overview

This API endpoint allows you to retrieve a list of staff members who have the role of "asatidz" (teacher) or "walikelas" (class advisor).

## API Endpoint

### Get Teachers and Advisors

```
GET /api/staff/teachers-advisors
```

Returns a list of all staff members who have either the "asatidz" or "walikelas" role.

**Response:**

```json
{
    "message": "data ditemukan",
    "status": 200,
    "data": [
        {
            "id": 1,
            "name": "Ahmad Guru",
            "email": "ahmad@example.com",
            "staff": {
                "id": 1,
                "user_id": 1,
                "code": "AS0001",
                "first_name": "Ahmad",
                "last_name": "Guru",
                "nik": "1234567890123456",
                "address": "Jl. Contoh Alamat No. 123",
                "phone": "081234567890",
                "zip_code": "12345",
                "created_at": "2025-08-31T10:00:00.000000Z",
                "updated_at": "2025-08-31T10:00:00.000000Z"
            },
            "roles": [
                {
                    "id": 3,
                    "name": "asatidz",
                    "guard_name": "api",
                    "created_at": "2025-08-31T10:00:00.000000Z",
                    "updated_at": "2025-08-31T10:00:00.000000Z"
                }
            ]
        },
        {
            "id": 2,
            "name": "Budi Wali",
            "email": "budi@example.com",
            "staff": {
                "id": 2,
                "user_id": 2,
                "code": "AS0002",
                "first_name": "Budi",
                "last_name": "Wali",
                "nik": "1234567890123457",
                "address": "Jl. Contoh Alamat No. 456",
                "phone": "081234567891",
                "zip_code": "12346",
                "created_at": "2025-08-31T10:00:00.000000Z",
                "updated_at": "2025-08-31T10:00:00.000000Z"
            },
            "roles": [
                {
                    "id": 4,
                    "name": "walikelas",
                    "guard_name": "api",
                    "created_at": "2025-08-31T10:00:00.000000Z",
                    "updated_at": "2025-08-31T10:00:00.000000Z"
                }
            ]
        }
    ]
}
```

### Error Responses

If no teachers or advisors are found, the API will return an empty data array:

```json
{
    "message": "data ditemukan",
    "status": 200,
    "data": []
}
```

If there's an error, the API will return:

```json
{
    "message": "terjadi kesalahan: [error details]"
}
```

Or if data is not found:

```json
{
    "message": "data tidak ditemukan"
}
```

## Usage Examples

### JavaScript (using fetch)

```javascript
fetch("/api/staff/teachers-advisors", {
    method: "GET",
    headers: {
        Authorization: "Bearer YOUR_JWT_TOKEN",
        "Content-Type": "application/json",
    },
})
    .then((response) => response.json())
    .then((data) => {
        console.log(data);
    })
    .catch((error) => {
        console.error("Error:", error);
    });
```

### PHP (using Guzzle)

```php
$client = new \GuzzleHttp\Client();
$response = $client->request('GET', '/api/staff/teachers-advisors', [
  'headers' => [
    'Authorization' => 'Bearer YOUR_JWT_TOKEN',
    'Accept' => 'application/json',
  ]
]);

$data = json_decode($response->getBody(), true);
print_r($data);
```

## Role Definitions

-   **asatidz**: Teachers who provide education to students
-   **walikelas**: Class advisors who oversee specific classes and student development

These roles are defined in the system's role management and can be assigned to staff members during creation or updated later.
