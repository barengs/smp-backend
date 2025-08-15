# Account Movement API Documentation

## Overview

Account Movement API adalah bagian dari sistem Bank Santri yang bertanggung jawab untuk mencatat dan mengelola pergerakan keuangan (movements) pada akun tabungan santri. API ini menangani pencatatan transaksi keuangan, setoran, penarikan, dan transfer antar akun dengan update saldo otomatis.

## Base URL

```
http://localhost:8000/api/account-movement
```

## Authentication

Semua endpoint memerlukan autentikasi JWT. Gunakan header:

```
Authorization: Bearer {your_jwt_token}
```

## Endpoints

### 1. Daftar Semua Movement

**GET** `/api/account-movement`

Menampilkan daftar semua pergerakan keuangan dalam sistem dengan filter dan pagination.

#### Query Parameters

| Parameter          | Type    | Required | Description                                                |
| ------------------ | ------- | -------- | ---------------------------------------------------------- |
| `account_number`   | string  | No       | Filter berdasarkan nomor akun                              |
| `transaction_type` | string  | No       | Filter berdasarkan jenis transaksi (CREDIT/DEBIT/TRANSFER) |
| `start_date`       | string  | No       | Filter tanggal mulai (YYYY-MM-DD)                          |
| `end_date`         | string  | No       | Filter tanggal akhir (YYYY-MM-DD)                          |
| `per_page`         | integer | No       | Jumlah item per halaman (default: 15)                      |

#### Response

```json
{
    "data": [
        {
            "id": 1,
            "account_number": "20250197001",
            "transaction_id": "uuid-string",
            "movement_time": "2025-01-15T10:30:00.000000Z",
            "description": "Setoran awal",
            "debit_amount": "0.00",
            "credit_amount": "100000.00",
            "balance_after_movement": "100000.00",
            "account": {
                "account_number": "20250197001",
                "customer": {
                    "name": "Ahmad Santoso"
                }
            },
            "transaction": {
                "transaction_type": {
                    "name": "Setoran Tunai"
                }
            }
        }
    ],
    "pagination": {
        "current_page": 1,
        "per_page": 15,
        "total": 100
    }
}
```

#### Status Codes

-   `200` - Success
-   `401` - Unauthenticated
-   `403` - Unauthorized

---

### 2. Buat Movement Baru

**POST** `/api/account-movement`

Membuat pergerakan keuangan baru (setoran, penarikan, atau transfer). Sistem akan otomatis mengupdate saldo akun dan mencatat transaksi.

#### Request Body

```json
{
    "account_number": "20250197001",
    "transaction_type_id": 1,
    "amount": 100000.0,
    "description": "Setoran awal",
    "reference_number": "REF001",
    "channel": "CASH",
    "destination_account": "20250197002"
}
```

#### Parameters

| Parameter             | Type    | Required | Description                                           |
| --------------------- | ------- | -------- | ----------------------------------------------------- |
| `account_number`      | string  | Yes      | Nomor akun tabungan                                   |
| `transaction_type_id` | integer | Yes      | ID jenis transaksi                                    |
| `amount`              | decimal | Yes      | Jumlah transaksi (positif=setoran, negatif=penarikan) |
| `description`         | string  | Yes      | Deskripsi transaksi                                   |
| `reference_number`    | string  | No       | Nomor referensi eksternal                             |
| `channel`             | string  | No       | Channel transaksi (CASH, TRANSFER, MOBILE)            |
| `destination_account` | string  | No       | Nomor akun tujuan (untuk transfer)                    |

#### Business Logic

-   **Setoran (Credit)**: `amount > 0` → `credit_amount` diisi, `debit_amount = 0`
-   **Penarikan (Debit)**: `amount < 0` → `debit_amount` diisi, `credit_amount = 0`
-   **Transfer**: Jika ada `destination_account`, sistem akan membuat 2 movement (debit di akun sumber, credit di akun tujuan)
-   **Validasi**: Akun harus berstatus ACTIVE, saldo mencukupi untuk penarikan
-   **Update Saldo**: Saldo akun otomatis diupdate setelah movement dibuat

#### Response

```json
{
    "data": {
        "id": 1,
        "account_number": "20250197001",
        "transaction_id": "uuid-string",
        "movement_time": "2025-01-15T10:30:00.000000Z",
        "description": "Setoran awal",
        "debit_amount": "0.00",
        "credit_amount": "100000.00",
        "balance_after_movement": "100000.00"
    },
    "message": "Movement created successfully"
}
```

#### Status Codes

-   `201` - Created
-   `400` - Bad Request
-   `404` - Account Not Found
-   `409` - Conflict (saldo tidak mencukupi, akun tidak aktif)
-   `422` - Validation Failed
-   `500` - Internal Server Error

---

### 3. Detail Movement

**GET** `/api/account-movement/{id}`

Menampilkan detail lengkap pergerakan keuangan berdasarkan ID.

#### Parameters

| Parameter | Type    | Required | Description |
| --------- | ------- | -------- | ----------- |
| `id`      | integer | Yes      | ID movement |

#### Response

```json
{
    "data": {
        "id": 1,
        "account_number": "20250197001",
        "transaction_id": "uuid-string",
        "movement_time": "2025-01-15T10:30:00.000000Z",
        "description": "Setoran awal",
        "debit_amount": "0.00",
        "credit_amount": "100000.00",
        "balance_after_movement": "100000.00",
        "account": {
            "account_number": "20250197001",
            "balance": "100000.00",
            "customer": {
                "name": "Ahmad Santoso",
                "nis": "20250197001"
            }
        },
        "transaction": {
            "transaction_type": {
                "name": "Setoran Tunai",
                "type": "CREDIT"
            },
            "channel": "CASH",
            "reference_number": "REF001"
        }
    }
}
```

#### Status Codes

-   `200` - Success
-   `404` - Movement Not Found
-   `401` - Unauthenticated

---

### 4. Update Movement

**PUT** `/api/account-movement/{id}`

Memperbarui informasi movement (hanya deskripsi yang dapat diubah). Amount dan balance tidak dapat diubah untuk menjaga integritas data.

#### Parameters

| Parameter | Type    | Required | Description |
| --------- | ------- | -------- | ----------- |
| `id`      | integer | Yes      | ID movement |

#### Request Body

```json
{
    "description": "Setoran awal bulan"
}
```

#### Parameters

| Parameter     | Type   | Required | Description                  |
| ------------- | ------ | -------- | ---------------------------- |
| `description` | string | Yes      | Deskripsi movement yang baru |

#### Response

```json
{
    "data": {
        "id": 1,
        "account_number": "20250197001",
        "description": "Setoran awal bulan",
        "debit_amount": "0.00",
        "credit_amount": "100000.00",
        "balance_after_movement": "100000.00"
    },
    "message": "Movement updated successfully"
}
```

#### Status Codes

-   `200` - Success
-   `400` - Bad Request
-   `404` - Movement Not Found
-   `422` - Validation Failed

---

### 5. Hapus Movement

**DELETE** `/api/account-movement/{id}`

Menghapus movement dari sistem. Hanya movement yang belum mempengaruhi saldo yang dapat dihapus.

#### Parameters

| Parameter | Type    | Required | Description                   |
| --------- | ------- | -------- | ----------------------------- |
| `id`      | integer | Yes      | ID movement yang akan dihapus |

#### Business Rules

-   Movement dengan `debit_amount > 0` atau `credit_amount > 0` tidak dapat dihapus
-   Hanya movement netral (tidak mempengaruhi saldo) yang dapat dihapus
-   Ini untuk menjaga integritas data keuangan

#### Response

```
204 No Content
```

#### Status Codes

-   `204` - No Content (Success)
-   `404` - Movement Not Found
-   `409` - Cannot delete movement that affects account balance
-   `401` - Unauthenticated

---

### 6. Riwayat Transaksi Akun

**GET** `/api/account-movement/account/{account_number}/history`

Menampilkan riwayat lengkap transaksi untuk akun tertentu dengan summary.

#### Parameters

| Parameter        | Type   | Required | Description         |
| ---------------- | ------ | -------- | ------------------- |
| `account_number` | string | Yes      | Nomor akun tabungan |

#### Query Parameters

| Parameter    | Type    | Required | Description                           |
| ------------ | ------- | -------- | ------------------------------------- |
| `start_date` | string  | No       | Filter tanggal mulai (YYYY-MM-DD)     |
| `end_date`   | string  | No       | Filter tanggal akhir (YYYY-MM-DD)     |
| `per_page`   | integer | No       | Jumlah item per halaman (default: 15) |

#### Response

```json
{
    "data": {
        "account": {
            "account_number": "20250197001",
            "balance": "150000.00",
            "customer": {
                "name": "Ahmad Santoso"
            }
        },
        "movements": [
            {
                "id": 1,
                "movement_time": "2025-01-15T10:30:00.000000Z",
                "description": "Setoran awal",
                "debit_amount": "0.00",
                "credit_amount": "100000.00",
                "balance_after_movement": "100000.00"
            }
        ],
        "summary": {
            "total_credit": "150000.00",
            "total_debit": "0.00",
            "transaction_count": 1
        }
    }
}
```

#### Status Codes

-   `200` - Success
-   `404` - Account Not Found

---

### 7. Rekap Transaksi Harian

**GET** `/api/account-movement/daily-summary`

Menampilkan rekap transaksi harian untuk periode tertentu. Berguna untuk laporan keuangan dan monitoring.

#### Query Parameters

| Parameter    | Type   | Required | Description                |
| ------------ | ------ | -------- | -------------------------- |
| `start_date` | string | Yes      | Tanggal mulai (YYYY-MM-DD) |
| `end_date`   | string | Yes      | Tanggal akhir (YYYY-MM-DD) |

#### Response

```json
{
    "data": [
        {
            "date": "2025-01-15",
            "total_credit": "500000.00",
            "total_debit": "200000.00",
            "net_amount": "300000.00",
            "transaction_count": 25
        }
    ]
}
```

#### Status Codes

-   `200` - Success
-   `422` - Validation Failed

---

## Data Models

### AccountMovement

```json
{
    "id": "integer",
    "account_number": "string",
    "transaction_id": "string (uuid)",
    "movement_time": "datetime",
    "description": "string",
    "debit_amount": "decimal",
    "credit_amount": "decimal",
    "balance_after_movement": "decimal",
    "created_at": "datetime",
    "updated_at": "datetime"
}
```

### AccountMovement with Relations

```json
{
    "id": "integer",
    "account_number": "string",
    "transaction_id": "string (uuid)",
    "movement_time": "datetime",
    "description": "string",
    "debit_amount": "decimal",
    "credit_amount": "decimal",
    "balance_after_movement": "decimal",
    "account": "Account",
    "transaction": "Transaction"
}
```

## Business Rules

1. **Integritas Data**: Amount dan balance tidak dapat diubah setelah movement dibuat
2. **Validasi Saldo**: Penarikan hanya dapat dilakukan jika saldo mencukupi
3. **Status Akun**: Hanya akun dengan status ACTIVE yang dapat melakukan transaksi
4. **Transfer**: Transfer antar akun akan membuat 2 movement (debit + credit)
5. **Penghapusan**: Movement yang mempengaruhi saldo tidak dapat dihapus
6. **Audit Trail**: Setiap movement mencatat waktu dan saldo setelah transaksi

## Transaction Types

### Credit Transactions (Setoran)

-   Setoran Tunai
-   Setoran Transfer
-   Bunga Tabungan
-   Bonus/Insentif

### Debit Transactions (Penarikan)

-   Penarikan Tunai
-   Penarikan Transfer
-   Biaya Administrasi
-   Pajak

### Transfer Transactions

-   Transfer Antar Akun
-   Transfer ke Bank Lain

## Examples

### cURL Examples

#### Get All Movements

```bash
curl -X GET "http://localhost:8000/api/account-movement" \
  -H "Authorization: Bearer {your_token}" \
  -H "Accept: application/json"
```

#### Create Credit Movement (Deposit)

```bash
curl -X POST "http://localhost:8000/api/account-movement" \
  -H "Authorization: Bearer {your_token}" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
    "account_number": "20250197001",
    "transaction_type_id": 1,
    "amount": 100000.00,
    "description": "Setoran awal",
    "channel": "CASH"
  }'
```

#### Create Debit Movement (Withdrawal)

```bash
curl -X POST "http://localhost:8000/api/account-movement" \
  -H "Authorization: Bearer {your_token}" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
    "account_number": "20250197001",
    "transaction_type_id": 2,
    "amount": -50000.00,
    "description": "Penarikan tunai",
    "channel": "CASH"
  }'
```

#### Get Account History

```bash
curl -X GET "http://localhost:8000/api/account-movement/account/20250197001/history?start_date=2025-01-01&end_date=2025-01-31" \
  -H "Authorization: Bearer {your_token}" \
  -H "Accept: application/json"
```

### JavaScript Examples

#### Create Movement

```javascript
const response = await fetch("http://localhost:8000/api/account-movement", {
    method: "POST",
    headers: {
        Authorization: `Bearer ${token}`,
        Accept: "application/json",
        "Content-Type": "application/json",
    },
    body: JSON.stringify({
        account_number: "20250197001",
        transaction_type_id: 1,
        amount: 100000.0,
        description: "Setoran awal",
        channel: "CASH",
    }),
});

const movement = await response.json();
```

#### Get Daily Summary

```javascript
const response = await fetch(
    "http://localhost:8000/api/account-movement/daily-summary?start_date=2025-01-01&end_date=2025-01-31",
    {
        method: "GET",
        headers: {
            Authorization: `Bearer ${token}`,
            Accept: "application/json",
        },
    }
);

const summary = await response.json();
```

## Error Handling

### Common Error Responses

#### Insufficient Balance

```json
{
    "message": "Insufficient balance. Available: 50000.00, Required: 100000.00"
}
```

#### Account Not Active

```json
{
    "message": "Account is not active. Current status: INACTIVE"
}
```

#### Validation Error

```json
{
    "message": "Validation failed",
    "errors": {
        "amount": ["The amount field is required."],
        "account_number": ["The selected account number is invalid."]
    }
}
```

## Testing

Untuk testing API ini, gunakan tools seperti:

-   Postman
-   Insomnia
-   cURL
-   Laravel Tinker
-   PHPUnit tests

### Test Scenarios

1. **Credit Movement**: Test setoran dengan berbagai channel
2. **Debit Movement**: Test penarikan dengan validasi saldo
3. **Transfer**: Test transfer antar akun
4. **Error Cases**: Test dengan akun tidak aktif, saldo tidak cukup
5. **Data Integrity**: Pastikan saldo selalu akurat setelah transaksi

## Support

Jika ada pertanyaan atau masalah dengan API ini, silakan hubungi tim development atau buat issue di repository project.
