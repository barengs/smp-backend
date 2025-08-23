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
        $chartOfAccounts = ChartOfAccount::with('children')->where('level', 'header')->orWhere('level', 'subheader')->get();
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

    /**
     * Generate COA code with 6-digit format
     *
     * @param string|null $parentCoaCode
     * @return string
     */
    public function generateCoaCode(?string $parentCoaCode = null): string
    {
        if ($parentCoaCode === null) {
            // For root level accounts, find the highest first digit and increment
            $existingCodes = ChartOfAccount::whereNull('parent_coa_code')
                ->pluck('coa_code')
                ->toArray();

            if (empty($existingCodes)) {
                return '100000';
            }

            // Extract first digits and find the maximum
            $firstDigits = array_map(fn($code) => (int) substr($code, 0, 1), $existingCodes);
            $maxFirstDigit = max($firstDigits);

            // If all first digits are 9, we need to handle this case
            if ($maxFirstDigit >= 9) {
                // Find the next available digit
                for ($i = 1; $i <= 9; $i++) {
                    if (!in_array($i, $firstDigits)) {
                        return $i . '00000';
                    }
                }
                // If all digits 1-9 are used, start with 1 again (this is a simplification)
                return '100000';
            }

            return ($maxFirstDigit + 1) . '00000';
        } else {
            // For child accounts, find the highest code with the same prefix and increment
            $prefixLength = strlen($parentCoaCode) - 5; // Assuming the last 5 digits are for incrementing
            $prefix = substr($parentCoaCode, 0, $prefixLength);

            // Find all child codes with the same prefix
            $childCodes = ChartOfAccount::where('parent_coa_code', $parentCoaCode)
                ->where('coa_code', 'like', $prefix . '%')
                ->pluck('coa_code')
                ->toArray();

            if (empty($childCodes)) {
                // If no children exist, create the first child code
                return $prefix . '10000';
            }

            // Extract the numeric part after the prefix and find the maximum
            $suffixes = array_map(fn($code) => (int) substr($code, $prefixLength), $childCodes);
            $maxSuffix = max($suffixes);

            // Increment the suffix and pad with zeros to make it 5 digits
            $newSuffix = str_pad($maxSuffix + 1, 5, '0', STR_PAD_LEFT);

            return $prefix . $newSuffix;
        }
    }
}
