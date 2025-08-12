<?php

namespace App\Http\Controllers\Api\Main;

use App\Http\Controllers\Controller;
use App\Models\ChartOfAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ChartOfAccountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $chartOfAccounts = ChartOfAccount::with('children')->whereNull('parent_coa_code')->get();
        return response()->json($chartOfAccounts);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'coa_code' => 'required|string|unique:chart_of_accounts,coa_code',
            'account_name' => 'required|string',
            'account_type' => 'required|in:ASSET,LIABILITY,EQUITY,INCOME,EXPENSE',
            'parent_coa_code' => 'nullable|string|exists:chart_of_accounts,coa_code',
            'is_postable' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $chartOfAccount = ChartOfAccount::create($request->all());

        return response()->json($chartOfAccount, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $chartOfAccount = ChartOfAccount::with('children')->findOrFail($id);
        return response()->json($chartOfAccount);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $chartOfAccount = ChartOfAccount::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'account_name' => 'required|string',
            'account_type' => 'required|in:ASSET,LIABILITY,EQUITY,INCOME,EXPENSE',
            'parent_coa_code' => 'nullable|string|exists:chart_of_accounts,coa_code',
            'is_postable' => 'required|boolean',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $chartOfAccount->update($request->all());

        return response()->json($chartOfAccount);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $chartOfAccount = ChartOfAccount::findOrFail($id);
        $chartOfAccount->delete();

        return response()->json(null, 204);
    }
}
