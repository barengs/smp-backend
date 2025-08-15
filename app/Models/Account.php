<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="Account",
 *     title="Account Model",
 *     description="Model untuk akun tabungan santri dalam sistem Bank Santri",
 *     @OA\Property(property="account_number", type="string", format="string", example="20250197001", description="Nomor akun tabungan (berdasarkan NIS siswa)"),
 *     @OA\Property(property="customer_id", type="integer", example=1, description="ID siswa pemilik akun"),
 *     @OA\Property(property="product_id", type="integer", example=1, description="ID produk keuangan"),
 *     @OA\Property(property="balance", type="number", format="decimal", example="150000.00", description="Saldo akun dalam rupiah"),
 *     @OA\Property(property="status", type="string", enum={"ACTIVE","DORMANT","CLOSED","BLOCKED","INACTIVE"}, example="ACTIVE", description="Status akun"),
 *     @OA\Property(property="open_date", type="string", format="date", example="2025-01-15", description="Tanggal pembukaan akun"),
 *     @OA\Property(property="close_date", type="string", format="date", nullable=true, example=null, description="Tanggal penutupan akun (jika status CLOSED)"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-01-15T10:30:00.000000Z", description="Waktu pembuatan record"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-01-15T10:30:00.000000Z", description="Waktu terakhir update record")
 * )
 * 
 * @OA\Schema(
 *     schema="AccountCreateRequest",
 *     title="Account Create Request",
 *     description="Request body untuk membuat akun baru",
 *     required={"student_id", "product_id"},
 *     @OA\Property(property="student_id", type="integer", example=1, description="ID siswa yang akan dibuatkan akun"),
 *     @OA\Property(property="product_id", type="integer", example=1, description="ID produk keuangan yang dipilih")
 * )
 * 
 * @OA\Schema(
 *     schema="AccountUpdateRequest",
 *     title="Account Update Request",
 *     description="Request body untuk update akun",
 *     required={"product_id", "status"},
 *     @OA\Property(property="product_id", type="integer", example=2, description="ID produk keuangan baru"),
 *     @OA\Property(property="status", type="string", enum={"ACTIVE","DORMANT","CLOSED","BLOCKED","INACTIVE"}, example="ACTIVE", description="Status akun baru")
 * )
 * 
 * @OA\Schema(
 *     schema="AccountStatusRequest",
 *     title="Account Status Update Request",
 *     description="Request body untuk update status akun",
 *     required={"status"},
 *     @OA\Property(property="status", type="string", enum={"ACTIVE","DORMANT","CLOSED","BLOCKED","INACTIVE"}, example="ACTIVE", description="Status akun baru")
 * )
 * 
 * @OA\Schema(
 *     schema="AccountWithRelations",
 *     title="Account with Relations",
 *     description="Akun dengan relasi customer, product, dan movements",
 *     allOf={
 *         @OA\Schema(ref="#/components/schemas/Account"),
 *         @OA\Schema(
 *             @OA\Property(property="customer", ref="#/components/schemas/Student"),
 *             @OA\Property(property="product", ref="#/components/schemas/Product"),
 *             @OA\Property(property="movements", type="array", @OA\Items(ref="#/components/schemas/AccountMovement"))
 *         )
 *     )
 * )
 * 
 * @OA\Schema(
 *     schema="AccountListResponse",
 *     title="Account List Response",
 *     description="Response untuk daftar akun",
 *     @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/AccountWithRelations"))
 * )
 * 
 * @OA\Schema(
 *     schema="AccountSingleResponse",
 *     title="Account Single Response",
 *     description="Response untuk single akun",
 *     @OA\Property(property="data", ref="#/components/schemas/AccountWithRelations")
 * )
 * 
 * @OA\Schema(
 *     schema="AccountCreateResponse",
 *     title="Account Create Response",
 *     description="Response untuk pembuatan akun berhasil",
 *     @OA\Property(property="data", ref="#/components/schemas/Account"),
 *     @OA\Property(property="message", type="string", example="Account created successfully")
 * )
 * 
 * @OA\Schema(
 *     schema="AccountUpdateResponse",
 *     title="Account Update Response",
 *     description="Response untuk update akun berhasil",
 *     @OA\Property(property="data", ref="#/components/schemas/Account"),
 *     @OA\Property(property="message", type="string", example="Account updated successfully")
 * )
 * 
 * @OA\Schema(
 *     schema="AccountStatusResponse",
 *     title="Account Status Update Response",
 *     description="Response untuk update status akun berhasil",
 *     @OA\Property(property="data", ref="#/components/schemas/Account"),
 *     @OA\Property(property="message", type="string", example="Account status updated successfully")
 * )
 * 
 * @OA\Schema(
 *     schema="ValidationErrorResponse",
 *     title="Validation Error Response",
 *     description="Response untuk error validasi",
 *     @OA\Property(property="message", type="string", example="Validation failed"),
 *     @OA\Property(property="errors", type="object", additionalProperties=@OA\Schema(type="array", @OA\Items(type="string")))
 * )
 * 
 * @OA\Schema(
 *     schema="ErrorResponse",
 *     title="Error Response",
 *     description="Response untuk error umum",
 *     @OA\Property(property="message", type="string", example="Error message"),
 *     @OA\Property(property="error", type="string", example="Error details", nullable=true)
 * )
 */
class Account extends Model
{
    protected $table = 'accounts';
    protected $primaryKey = 'account_number';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'account_number',
        'customer_id',
        'product_id',
        'balance',
        'status',
        'open_date',
        'close_date',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'open_date' => 'date',
        'close_date' => 'date',
    ];

    /**
     * Get the customer that owns the Account.
     */
    public function customer()
    {
        return $this->belongsTo(Student::class, 'customer_id', 'id');
    }

    /**
     * Get the product that defines the Account.
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    /**
     * Get all of the movements for the Account.
     */
    public function movements()
    {
        return $this->hasMany(AccountMovement::class, 'account_number', 'account_number');
    }
}
