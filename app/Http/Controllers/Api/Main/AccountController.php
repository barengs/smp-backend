<?php

namespace App\Http\Controllers\Api\Main;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AccountController extends Controller
{
    /**
     * Membuat rekening baru untuk siswa.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createAccount(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'student_id' => 'required|exists:students,id',
            'product_id' => 'required|exists:products,id',
            'hijri_year' => 'required|digits:4',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        try {
            $student = Student::findOrFail($request->student_id);
            $hijriYear = $request->hijri_year;
            $prefix = $hijriYear . '0197';

            // Find the last account number with the same prefix and increment the sequence
            $lastAccount = Account::where('account_number', 'like', $prefix . '%')
                ->orderBy('account_number', 'desc')
                ->first();

            $sequence = $lastAccount ? (int) substr($lastAccount->account_number, -3) + 1 : 1;
            $accountNumber = $prefix . str_pad($sequence, 3, '0', STR_PAD_LEFT);

            $account = Account::create([
                'account_number' => $accountNumber,
                'customer_id' => $student->id,
                'product_id' => $request->product_id,
                'balance' => 0,
                'status' => 'INACTIVE',
                'open_date' => now(),
            ]);

            return response()->json($account, 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to create account', 'error' => $e->getMessage()], 500);
        }
    }
}
