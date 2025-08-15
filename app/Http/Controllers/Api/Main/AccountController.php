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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $accounts = Account::with(['customer', 'product'])->paginate(10);
        return response()->json($accounts);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'student_id' => 'required|exists:students,id',
            'product_id' => 'required|exists:products,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        try {
            $student = Student::findOrFail($request->student_id);
            // Check if the student already has an account
            if (Account::where('customer_id', $student->id)->exists()) {
                return response()->json(['message' => 'Student already has an account'], 409);
            }

            // Create a new account for the student
            $account = Account::create([
                'account_number' => $student->nis,
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

    /**
     * Display the specified resource.
     *
     * @param  string  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(string $id)
    {
        try {
            $account = Account::with(['customer', 'product', 'movements'])->findOrFail($id);
            return response()->json($account);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Account not found'], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'status' => 'required|in:ACTIVE,DORMANT,CLOSED,BLOCKED,INACTIVE',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        try {
            $account = Account::findOrFail($id);
            $account->update($request->only(['product_id', 'status']));
            return response()->json($account);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Account not found'], 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  string  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(string $id)
    {
        try {
            $account = Account::findOrFail($id);
            $account->delete();
            return response()->json(null, 204);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Account not found'], 404);
        }
    }

    /**
     * Update the status of the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateStatus(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:ACTIVE,DORMANT,CLOSED,BLOCKED,INACTIVE',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        try {
            $account = Account::findOrFail($id);
            $account->status = $request->status;
            $account->save();
            return response()->json($account);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Account not found'], 404);
        }
    }
}
