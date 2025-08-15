# Account API Documentation

## Overview

Account API adalah bagian dari sistem Bank Santri yang bertanggung jawab untuk mengelola akun tabungan santri. API ini menyediakan endpoint untuk membuat, membaca, mengupdate, dan menghapus akun tabungan.

## Base URL

```
http://localhost:8000/api/account
```

## Authentication

Semua endpoint memerlukan autentikasi JWT. Gunakan header:

```
Authorization: Bearer {your_jwt_token}
```

## Endpoints

### 1. Daftar Semua Akun

**GET** `/api/account`

Menampilkan daftar semua akun tabungan santri yang ada dalam sistem.

#### Response

```json
{
    "data": [
        {
            "account_number": "20250197001",
            "customer_id": 1,
            "product_id": 1,
            "balance": "150000.00",
            "status": "ACTIVE",
            "open_date": "2025-01-15",
            "close_date": null,
            "customer": {
                "id": 1,
                "name": "Ahmad Santoso",
                "nis": "20250197001"
            },
            "product": {
                "id": 1,
                "name": "Tabungan Santri",
                "type": "SAVINGS"
            }
        }
    ]
}
```

#### Status Codes

-   `200` - Success
-   `401` - Unauthenticated
-   `403` - Unauthorized

---

### 2. Buat Akun Baru

**POST** `/api/account`

Membuat akun tabungan baru untuk santri. Sistem akan otomatis generate nomor akun berdasarkan NIS siswa.

#### Request Body

```json
{
    "student_id": 1,
    "product_id": 1
}
```

#### Parameters

| Parameter    | Type    | Required | Description                       |
| ------------ | ------- | -------- | --------------------------------- |
| `student_id` | integer | Yes      | ID siswa yang akan dibuatkan akun |
| `product_id` | integer | Yes      | ID produk keuangan yang dipilih   |

#### Response

```json
{
    "data": {
        "account_number": "20250197001",
        "customer_id": 1,
        "product_id": 1,
        "balance": "0.00",
        "status": "INACTIVE",
        "open_date": "2025-01-15T10:30:00.000000Z",
        "close_date": null
    },
    "message": "Account created successfully"
}
```

#### Status Codes

-   `201` - Created
-   `400` - Bad Request
-   `409` - Conflict (Student already has an account)
-   `422` - Validation Failed
-   `500` - Internal Server Error

---

### 3. Detail Akun

**GET** `/api/account/{id}`

Menampilkan detail lengkap akun tabungan berdasarkan nomor akun.

#### Parameters

| Parameter | Type   | Required | Description         |
| --------- | ------ | -------- | ------------------- |
| `id`      | string | Yes      | Nomor akun tabungan |

#### Response

```json
{
    "data": {
        "account_number": "20250197001",
        "customer_id": 1,
        "product_id": 1,
        "balance": "150000.00",
        "status": "ACTIVE",
        "open_date": "2025-01-15",
        "close_date": null,
        "customer": {
            "id": 1,
            "name": "Ahmad Santoso",
            "nis": "20250197001",
            "class": "VII A"
        },
        "product": {
            "id": 1,
            "name": "Tabungan Santri",
            "type": "SAVINGS",
            "description": "Produk tabungan khusus untuk santri"
        },
        "movements": [
            {
                "id": 1,
                "transaction_type": "CREDIT",
                "amount": "100000.00",
                "description": "Setoran awal",
                "created_at": "2025-01-15T10:30:00.000000Z"
            }
        ]
    }
}
```

#### Status Codes

-   `200` - Success
-   `404` - Account Not Found
-   `401` - Unauthenticated

---

### 4. Update Akun

**PUT** `/api/account/{id}`

Memperbarui informasi akun tabungan seperti produk keuangan dan status akun.

#### Parameters

| Parameter | Type   | Required | Description         |
| --------- | ------ | -------- | ------------------- |
| `id`      | string | Yes      | Nomor akun tabungan |

#### Request Body

```json
{
    "product_id": 2,
    "status": "ACTIVE"
}
```

#### Parameters

| Parameter    | Type    | Required | Description             |
| ------------ | ------- | -------- | ----------------------- |
| `product_id` | integer | Yes      | ID produk keuangan baru |
| `status`     | string  | Yes      | Status akun baru        |

#### Status Values

-   `ACTIVE` - Akun aktif
-   `DORMANT` - Akun tidak aktif sementara
-   `CLOSED` - Akun ditutup
-   `BLOCKED` - Akun diblokir
-   `INACTIVE` - Akun tidak aktif

#### Response

```json
{
    "data": {
        "account_number": "20250197001",
        "customer_id": 1,
        "product_id": 2,
        "balance": "150000.00",
        "status": "ACTIVE",
        "open_date": "2025-01-15",
        "close_date": null
    },
    "message": "Account updated successfully"
}
```

#### Status Codes

-   `200` - Success
-   `400` - Bad Request
-   `404` - Account Not Found
-   `422` - Validation Failed

---

### 5. Hapus Akun

**DELETE** `/api/account/{id}`

Menghapus akun tabungan dari sistem. Hanya dapat dihapus jika saldo 0 dan tidak ada transaksi.

#### Parameters

| Parameter | Type   | Required | Description                           |
| --------- | ------ | -------- | ------------------------------------- |
| `id`      | string | Yes      | Nomor akun tabungan yang akan dihapus |

#### Response

```
204 No Content
```

#### Status Codes

-   `204` - No Content (Success)
-   `404` - Account Not Found
-   `409` - Cannot delete account with active balance or transactions
-   `401` - Unauthenticated

---

### 6. Update Status Akun

**POST** `/api/account/{id}/status`

Memperbarui status akun tabungan tanpa mengubah informasi lainnya.

#### Parameters

| Parameter | Type   | Required | Description         |
| --------- | ------ | -------- | ------------------- |
| `id`      | string | Yes      | Nomor akun tabungan |

#### Request Body

```json
{
    "status": "ACTIVE"
}
```

#### Parameters

| Parameter | Type   | Required | Description      |
| --------- | ------ | -------- | ---------------- |
| `status`  | string | Yes      | Status akun baru |

#### Status Values

-   `ACTIVE` - Akun aktif
-   `DORMANT` - Akun tidak aktif sementara
-   `CLOSED` - Akun ditutup
-   `BLOCKED` - Akun diblokir
-   `INACTIVE` - Akun tidak aktif

#### Response

```json
{
    "data": {
        "account_number": "20250197001",
        "customer_id": 1,
        "product_id": 1,
        "balance": "150000.00",
        "status": "ACTIVE",
        "open_date": "2025-01-15",
        "close_date": null
    },
    "message": "Account status updated successfully"
}
```

#### Status Codes

-   `200` - Success
-   `400` - Bad Request
-   `404` - Account Not Found
-   `409` - Cannot change status to CLOSED with active balance
-   `422` - Validation Failed

---

## Data Models

### Account

```json
{
    "account_number": "string",
    "customer_id": "integer",
    "product_id": "integer",
    "balance": "decimal",
    "status": "string",
    "open_date": "date",
    "close_date": "date|null",
    "created_at": "datetime",
    "updated_at": "datetime"
}
```

### Account with Relations

```json
{
    "account_number": "string",
    "customer_id": "integer",
    "product_id": "integer",
    "balance": "decimal",
    "status": "string",
    "open_date": "date",
    "close_date": "date|null",
    "customer": "Student",
    "product": "Product",
    "movements": "AccountMovement[]"
}
```

## Error Responses

### Validation Error

```json
{
    "message": "Validation failed",
    "errors": {
        "student_id": ["The student id field is required."],
        "product_id": ["The selected product id is invalid."]
    }
}
```

### General Error

```json
{
    "message": "Error message",
    "error": "Error details"
}
```

## Business Rules

1. **Akun Unik**: Setiap siswa hanya dapat memiliki satu akun tabungan
2. **Status Awal**: Akun baru dibuat dengan status `INACTIVE`
3. **Penghapusan**: Akun hanya dapat dihapus jika saldo 0 dan tidak ada transaksi
4. **Status CLOSED**: Akun tidak dapat diubah ke status `CLOSED` jika masih memiliki saldo
5. **Nomor Akun**: Nomor akun otomatis di-generate berdasarkan NIS siswa

## Examples

### cURL Examples

#### Get All Accounts

```bash
curl -X GET "http://localhost:8000/api/account" \
  -H "Authorization: Bearer {your_token}" \
  -H "Accept: application/json"
```

#### Create Account

```bash
curl -X POST "http://localhost:8000/api/account" \
  -H "Authorization: Bearer {your_token}" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
    "student_id": 1,
    "product_id": 1
  }'
```

#### Update Account Status

```bash
curl -X POST "http://localhost:8000/api/account/20250197001/status" \
  -H "Authorization: Bearer {your_token}" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
    "status": "ACTIVE"
  }'
```

### JavaScript Examples

#### Get All Accounts

```javascript
const response = await fetch("http://localhost:8000/api/account", {
    method: "GET",
    headers: {
        Authorization: `Bearer ${token}`,
        Accept: "application/json",
    },
});

const accounts = await response.json();
```

#### Create Account

```javascript
const response = await fetch("http://localhost:8000/api/account", {
    method: "POST",
    headers: {
        Authorization: `Bearer ${token}`,
        Accept: "application/json",
        "Content-Type": "application/json",
    },
    body: JSON.stringify({
        student_id: 1,
        product_id: 1,
    }),
});

const account = await response.json();
```

## Testing

Untuk testing API ini, gunakan tools seperti:

-   Postman
-   Insomnia
-   cURL
-   Laravel Tinker
-   PHPUnit tests

## Support

Jika ada pertanyaan atau masalah dengan API ini, silakan hubungi tim development atau buat issue di repository project.
