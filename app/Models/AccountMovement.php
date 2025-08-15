<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="AccountMovement",
 *     title="Account Movement Model",
 *     description="Model untuk pergerakan keuangan pada akun tabungan santri",
 *     @OA\Property(property="id", type="integer", example=1, description="ID movement"),
 *     @OA\Property(property="account_number", type="string", format="string", example="20250197001", description="Nomor akun tabungan"),
 *     @OA\Property(property="transaction_id", type="string", format="uuid", example="uuid-string", description="ID transaksi"),
 *     @OA\Property(property="movement_time", type="string", format="date-time", example="2025-01-15T10:30:00.000000Z", description="Waktu pergerakan"),
 *     @OA\Property(property="description", type="string", example="Setoran awal", description="Deskripsi pergerakan"),
 *     @OA\Property(property="debit_amount", type="number", format="decimal", example="0.00", description="Jumlah debit (penarikan)"),
 *     @OA\Property(property="credit_amount", type="number", format="decimal", example="100000.00", description="Jumlah credit (setoran)"),
 *     @OA\Property(property="balance_after_movement", type="number", format="decimal", example="100000.00", description="Saldo setelah pergerakan"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-01-15T10:30:00.000000Z", description="Waktu pembuatan record"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-01-15T10:30:00.000000Z", description="Waktu terakhir update record")
 * )
 *
 * @OA\Schema(
 *     schema="AccountMovementCreateRequest",
 *     title="Account Movement Create Request",
 *     description="Request body untuk membuat movement baru",
 *     required={"account_number", "transaction_type_id", "amount", "description"},
 *     @OA\Property(property="account_number", type="string", example="20250197001", description="Nomor akun tabungan"),
 *     @OA\Property(property="transaction_type_id", type="integer", example=1, description="ID jenis transaksi"),
 *     @OA\Property(property="amount", type="number", format="decimal", example="100000.00", description="Jumlah transaksi (positif=setoran, negatif=penarikan)"),
 *     @OA\Property(property="description", type="string", example="Setoran awal", description="Deskripsi transaksi"),
 *     @OA\Property(property="reference_number", type="string", example="REF001", description="Nomor referensi eksternal"),
 *     @OA\Property(property="channel", type="string", enum={"CASH","TRANSFER","MOBILE"}, example="CASH", description="Channel transaksi"),
 *     @OA\Property(property="destination_account", type="string", example="20250197002", description="Nomor akun tujuan (untuk transfer)")
 * )
 *
 * @OA\Schema(
 *     schema="AccountMovementUpdateRequest",
 *     title="Account Movement Update Request",
 *     description="Request body untuk update movement",
 *     required={"description"},
 *     @OA\Property(property="description", type="string", example="Setoran awal bulan", description="Deskripsi movement yang baru")
 * )
 *
 * @OA\Schema(
 *     schema="AccountMovementWithRelations",
 *     title="Account Movement with Relations",
 *     description="Movement dengan relasi account dan transaction",
 *     allOf={
 *         @OA\Schema(ref="#/components/schemas/AccountMovement"),
 *         @OA\Schema(
 *             @OA\Property(property="account", ref="#/components/schemas/Account"),
 *             @OA\Property(property="transaction", ref="#/components/schemas/Transaction")
 *         )
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="AccountMovementListResponse",
 *     title="Account Movement List Response",
 *     description="Response untuk daftar movements",
 *     @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/AccountMovementWithRelations")),
 *     @OA\Property(property="pagination", ref="#/components/schemas/PaginationResponse")
 * )
 *
 * @OA\Schema(
 *     schema="AccountMovementSingleResponse",
 *     title="Account Movement Single Response",
 *     description="Response untuk single movement",
 *     @OA\Property(property="data", ref="#/components/schemas/AccountMovementWithRelations")
 * )
 *
 * @OA\Schema(
 *     schema="AccountMovementCreateResponse",
 *     title="Account Movement Create Response",
 *     description="Response untuk pembuatan movement berhasil",
 *     @OA\Property(property="data", ref="#/components/schemas/AccountMovementWithRelations"),
 *     @OA\Property(property="message", type="string", example="Movement created successfully")
 * )
 *
 * @OA\Schema(
 *     schema="AccountMovementUpdateResponse",
 *     title="Account Movement Update Response",
 *     description="Response untuk update movement berhasil",
 *     @OA\Property(property="data", ref="#/components/schemas/AccountMovement"),
 *     @OA\Property(property="message", type="string", example="Movement updated successfully")
 * )
 *
 * @OA\Schema(
 *     schema="AccountMovementHistoryResponse",
 *     title="Account Movement History Response",
 *     description="Response untuk riwayat transaksi akun",
 *     @OA\Property(property="data", type="object", properties={
 *         @OA\Property(property="account", ref="#/components/schemas/Account"),
 *         @OA\Property(property="movements", type="array", @OA\Items(ref="#/components/schemas/AccountMovement")),
 *         @OA\Property(property="summary", type="object", properties={
 *             @OA\Property(property="total_credit", type="number", format="decimal", example="150000.00"),
 *             @OA\Property(property="total_debit", type="number", format="decimal", example="0.00"),
 *             @OA\Property(property="transaction_count", type="integer", example=1)
 *         })
 *     })
 * )
 *
 * @OA\Schema(
 *     schema="DailySummaryResponse",
 *     title="Daily Summary Response",
 *     description="Response untuk rekap transaksi harian",
 *     @OA\Property(property="data", type="array", @OA\Items(type="object", properties={
 *         @OA\Property(property="date", type="string", format="date", example="2025-01-15"),
 *         @OA\Property(property="total_credit", type="number", format="decimal", example="500000.00"),
 *         @OA\Property(property="total_debit", type="number", format="decimal", example="200000.00"),
 *         @OA\Property(property="net_amount", type="number", format="decimal", example="300000.00"),
 *         @OA\Property(property="transaction_count", type="integer", example=25)
 *     }))
 * )
 *
 * @OA\Schema(
 *     schema="PaginationResponse",
 *     title="Pagination Response",
 *     description="Response pagination standar",
 *     @OA\Property(property="current_page", type="integer", example=1),
 *     @OA\Property(property="per_page", type="integer", example=15),
 *     @OA\Property(property="total", type="integer", example=100)
 * )
 */
class AccountMovement extends Model
{
    protected $table = 'account_movements';

    // Define the fillable attributes
    protected $fillable = [
        'account_number',
        'transaction_id',
        'movement_time',
        'description',
        'debit_amount',
        'credit_amount',
        'balance_after_movement',
    ];

    protected $casts = [
        'debit_amount' => 'decimal:2',
        'credit_amount' => 'decimal:2',
        'balance_after_movement' => 'decimal:2',
        'movement_time' => 'datetime',
    ];

    /**
     * Get the account that owns the movement.
     */
    public function account()
    {
        return $this->belongsTo(Account::class, 'account_number', 'account_number');
    }

    /**
     * Get the transaction associated with the movement.
     */
    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'transaction_id', 'id');
    }

    /**
     * Scope for credit movements (deposits)
     */
    public function scopeCredits($query)
    {
        return $query->where('credit_amount', '>', 0);
    }

    /**
     * Scope for debit movements (withdrawals)
     */
    public function scopeDebits($query)
    {
        return $query->where('debit_amount', '>', 0);
    }

    /**
     * Scope for movements in date range
     */
    public function scopeInDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('movement_time', [$startDate, $endDate]);
    }

    /**
     * Scope for movements by account
     */
    public function scopeByAccount($query, $accountNumber)
    {
        return $query->where('account_number', $accountNumber);
    }

    /**
     * Get the movement type (CREDIT or DEBIT)
     */
    public function getMovementTypeAttribute()
    {
        if ($this->credit_amount > 0) {
            return 'CREDIT';
        } elseif ($this->debit_amount > 0) {
            return 'DEBIT';
        }
        return 'NEUTRAL';
    }

    /**
     * Get the movement amount (positive for credit, negative for debit)
     */
    public function getMovementAmountAttribute()
    {
        if ($this->credit_amount > 0) {
            return $this->credit_amount;
        } elseif ($this->debit_amount > 0) {
            return -$this->debit_amount;
        }
        return 0;
    }

    /**
     * Check if movement affects balance
     */
    public function affectsBalance()
    {
        return $this->credit_amount > 0 || $this->debit_amount > 0;
    }

    /**
     * Get formatted movement time
     */
    public function getFormattedMovementTimeAttribute()
    {
        return $this->movement_time->format('d/m/Y H:i:s');
    }

    /**
     * Get movement description with amount
     */
    public function getDescriptionWithAmountAttribute()
    {
        $amount = number_format(abs($this->movement_amount), 0, ',', '.');
        $type = $this->movement_type === 'CREDIT' ? 'Setoran' : 'Penarikan';

        return "{$type} Rp {$amount} - {$this->description}";
    }
}
