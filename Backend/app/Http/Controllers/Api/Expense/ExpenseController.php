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
        

        return response()->json([
            'success' => true,
            'message' => "Get all expense category & sub-category.",
            'data' => [
                'categories' => $categories,
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
}
