<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Expense;
use Illuminate\Support\Facades\Validator;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $query = Expense::query();
        
        // Filter by month/date if needed
        if ($request->month && $request->year) {
             $query->whereMonth('date', $request->month)
                   ->whereYear('date', $request->year);
        }

        $expenses = $query->latest('date')->latest('created_at')->get();
        // Map date to expense_date for frontend compatibility if needed, 
        // or just ensure frontend uses what's returned. 
        // Mobile app expects 'expense_date' in JSON.
        $expenses->transform(function ($expense) {
            $expense->expense_date = $expense->date; // Add alias
            return $expense;
        });

        return response()->json(['data' => $expenses]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $expense = Expense::create([
            'name' => $request->name,
            'amount' => $request->amount,
            'date' => $request->expense_date, // Map to correct column
            'description' => $request->description,
            // 'user_id' => $request->user()->id ?? null,
        ]);
        
        $expense->expense_date = $expense->date; // for response

        return response()->json(['message' => 'Expense created successfully', 'data' => $expense], 201);
    }

    public function update(Request $request, $id)
    {
        $expense = Expense::find($id);

        if (!$expense) {
            return response()->json(['message' => 'Expense not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $expense->update([
            'name' => $request->name,
            'amount' => $request->amount,
            'date' => $request->expense_date, // Map to correct column
            'description' => $request->description,
        ]);
        
        $expense->expense_date = $expense->date; // for response

        return response()->json(['message' => 'Expense updated successfully', 'data' => $expense], 200);
    }

    public function destroy($id)
    {
        $expense = Expense::find($id);

        if (!$expense) {
            return response()->json(['message' => 'Expense not found'], 404);
        }

        $expense->delete();

        return response()->json(['message' => 'Expense deleted successfully'], 200);
    }
}
