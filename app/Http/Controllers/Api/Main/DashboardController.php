<?php

namespace App\Http\Controllers\Api\Main;

use App\Models\Employee;
use App\Models\Student;
use App\Models\Transaction;
use App\Models\Product;
use App\Models\Account;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    /**
     * Menampilkan dashboard utama aplikasi manajemen pesantren
     *
     * Method ini digunakan untuk menampilkan ringkasan data pesantren yang penting
     * seperti jumlah santri, asatidz, transaksi keuangan, dan statistik lainnya.
     * Data ini berguna untuk monitoring dan analisis kinerja pesantren.
     *
     * @group Dashboard
     * @authenticated
     *
     * @response 200 {
     *   "status": "success",
     *   "message": "Dashboard data retrieved successfully",
     *   "data": {
     *     "santri": 150,
     *     "asatidz": 25,
     *     "tugasan": 10,
     *     "alumni": 50,
     *     "total_customers": 150,
     *     "total_accounts": 200,
     *     "total_transactions": 1250,
     *     "total_products": 8,
     *     "active_products": 6,
     *     "today_transactions": 45,
     *     "monthly_transactions": 320,
     *     "total_balance": 1500000000,
     *     "transaction_summary": {
     *       "deposits": 25,
     *       "withdrawals": 15,
     *       "transfers": 5
     *     }
     *   }
     * }
     *
     * @response 401 {
     *   "status": "error",
     *   "message": "Unauthorized"
     * }
     *
     * @response 500 {
     *   "status": "error",
     *   "message": "Failed to retrieve dashboard data"
     * }
     */
    public function index()
    {
        try {
            // Data untuk sistem pesantren (existing)
            $santri = Student::where("status", 'Aktif')->count();
            $asatidz = Employee::count();
            $tugasan = Student::where("status", 'Tugas')->count();
            $alumni = Student::where("status", 'Alumni')->count();

            // Data untuk sistem perbankan (new)
            $totalCustomers = Student::count(); // Using students as customers for now
            $totalAccounts = Account::count();
            $totalTransactions = Transaction::count();
            $totalProducts = Product::count();
            $activeProducts = Product::where('is_active', true)->count();

            // Today's transactions
            $todayTransactions = Transaction::whereDate('created_at', today())->count();

            // Monthly transactions
            $monthlyTransactions = Transaction::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count();

            // Total balance across all accounts
            $totalBalance = Account::sum('balance');

            // Transaction summary for today
            $transactionSummary = [
                'deposits' => Transaction::where('transaction_type', 'CASH_DEPOSIT')
                    ->whereDate('created_at', today())
                    ->count(),
                'withdrawals' => Transaction::where('transaction_type', 'CASH_WITHDRAWAL')
                    ->whereDate('created_at', today())
                    ->count(),
                'transfers' => Transaction::where('transaction_type', 'FUND_TRANSFER')
                    ->whereDate('created_at', today())
                    ->count(),
            ];

            return response()->json([
                'status' => 'success',
                'message' => 'Dashboard data retrieved successfully',
                'data' => [
                    // Existing data for pesantren system
                    'santri' => $santri,
                    'asatidz' => $asatidz,
                    'tugasan' => $tugasan,
                    'alumni' => $alumni,

                    // New data for banking system
                    'total_customers' => $totalCustomers,
                    'total_accounts' => $totalAccounts,
                    'total_transactions' => $totalTransactions,
                    'total_products' => $totalProducts,
                    'active_products' => $activeProducts,
                    'today_transactions' => $todayTransactions,
                    'monthly_transactions' => $monthlyTransactions,
                    'total_balance' => $totalBalance,
                    'transaction_summary' => $transactionSummary,
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve dashboard data: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Menampilkan statistik transaksi per periode
     *
     * Method ini digunakan untuk menampilkan statistik transaksi dalam periode tertentu.
     * Berguna untuk analisis trend dan laporan manajemen.
     *
     * @group Dashboard
     * @authenticated
     *
     * @queryParam period string required Periode statistik (daily, weekly, monthly, yearly). Example: monthly
     * @queryParam start_date string Start date untuk custom period (YYYY-MM-DD). Example: 2024-01-01
     * @queryParam end_date string End date untuk custom period (YYYY-MM-DD). Example: 2024-12-31
     *
     * @response 200 {
     *   "status": "success",
     *   "message": "Transaction statistics retrieved successfully",
     *   "data": {
     *     "period": "monthly",
     *     "total_transactions": 320,
     *     "total_amount": 150000000,
     *     "average_amount": 468750,
     *     "transaction_types": {
     *       "CASH_DEPOSIT": 150,
     *       "CASH_WITHDRAWAL": 120,
     *       "FUND_TRANSFER": 50
     *     }
     *   }
     * }
     */
    public function transactionStatistics(Request $request)
    {
        try {
            $request->validate([
                'period' => 'required|in:daily,weekly,monthly,yearly',
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
            ]);

            $period = $request->period;
            $startDate = $request->start_date;
            $endDate = $request->end_date;

            // Build query based on period
            $query = Transaction::query();

            if ($startDate && $endDate) {
                $query->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            } else {
                switch ($period) {
                    case 'daily':
                        $query->whereDate('created_at', today());
                        break;
                    case 'weekly':
                        $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                        break;
                    case 'monthly':
                        $query->whereMonth('created_at', now()->month)
                            ->whereYear('created_at', now()->year);
                        break;
                    case 'yearly':
                        $query->whereYear('created_at', now()->year);
                        break;
                }
            }

            $totalTransactions = $query->count();
            $totalAmount = $query->sum('amount');
            $averageAmount = $totalTransactions > 0 ? $totalAmount / $totalTransactions : 0;

            // Transaction types breakdown
            $transactionTypes = $query->selectRaw('transaction_type, COUNT(*) as count')
                ->groupBy('transaction_type')
                ->pluck('count', 'transaction_type')
                ->toArray();

            return response()->json([
                'status' => 'success',
                'message' => 'Transaction statistics retrieved successfully',
                'data' => [
                    'period' => $period,
                    'total_transactions' => $totalTransactions,
                    'total_amount' => $totalAmount,
                    'average_amount' => round($averageAmount, 2),
                    'transaction_types' => $transactionTypes,
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve transaction statistics: ' . $e->getMessage(),
            ], 500);
        }
    }
    /**
     * Menampilkan statistik santri berdasarkan periode (angkatan).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function studentStatisticsByPeriod(Request $request)
    {
        try {
            $statistics = Student::select('period', \DB::raw('count(*) as total'))
                ->groupBy('period')
                ->orderBy('period', 'asc')
                ->get();

            return response()->json([
                'status' => 'success',
                'message' => 'Student statistics by period retrieved successfully',
                'data' => $statistics,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve student statistics: ' . $e->getMessage(),
            ], 500);
        }
    }
}
