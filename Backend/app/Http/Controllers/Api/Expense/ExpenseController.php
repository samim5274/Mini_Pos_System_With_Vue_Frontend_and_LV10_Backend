<?php

namespace App\Http\Controllers\Api\Expense;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use App\Models\Excategory;
use App\Models\Exsubcategory;
use App\Models\Expense;

class ExpenseController extends Controller
{
    public function index(){
        
        $categories = Excategory::all();
        $expenseDetails = Expense::with(['category','subcategory','user'])->whereDate('date', today())->latest()->paginate(10);

        return response()->json([
            'success' => true,
            'message' => "Get all expense category & sub-category.",
            'data' => [
                'categories' => $categories,
                'expenseDetails' => $expenseDetails,
            ]
        ]);
    }

    public function getSubCategory($id){
        try {
            $subcategories = Exsubcategory::where('category_id', $id)->get();
            return response()->json([
                'success' => true,
                'data' => $subcategories
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
            ], 500);
        }
    }

    public function store(Request $request){
        $request->validate([
            'category_id'      => ['required', 'integer', 'exists:excategories,id'],
            'sub_category_id'  => ['required', 'integer', 'exists:exsubcategories,id'],
            'title'            => ['required', 'string', 'max:255'],
            'amount'           => ['required', 'numeric', 'min:0'],
            'remark'           => ['nullable', 'string', 'max:1000'],
        ]);

        $userId = Auth::id();

        $expense = Expense::create([
            'category_id'     => $request->category_id,
            'sub_category_id' => $request->sub_category_id,
            'title'           => $request->title,
            'date'            => now()->toDateString(),
            'amount'          => $request->amount,
            'remark'          => $request->remark ?? "",
            'user_id'         => $userId,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Expense created successfully.',
            'data'    => $expense,
        ], 201);
    }
}
