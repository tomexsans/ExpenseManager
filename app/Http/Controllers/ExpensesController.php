<?php

namespace App\Http\Controllers;

use App\Models\ExpensesModel;
use Illuminate\Http\Request;

use \App\Http\Requests\AddExpenses;
use Illuminate\Support\Facades\Auth;

class ExpensesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return ExpensesModel::with(['category'=>function($query){
            $query->select('id','category_name');
        }])
        ->where('user_id',\Auth::id())
        ->paginate(15,['id','user_id','expense_category_id','amount','entry_date','created_at as created']);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AddExpenses $request)
    {
        //

        $validated = $request->validated();

        $expenses = new ExpensesModel;
        $expenses->user_id = $validated['id'] ?? Auth::id();
        $expenses->expense_category_id = $validated['category'];
        $expenses->amount = $validated['amount'];
        $expenses->entry_date = $validated['entry_date'];

        $stat = $expenses->save();

        if($stat){
            return response()->json(['message'=>'Expenses saved'], 200);
        }
        return response()->json(['message'=>'Unable to Add new expenses'], 400);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ExpensesModel  $expense
     * @return \Illuminate\Http\Response
     */
    public function show(ExpensesModel $expense)
    {
        return $expense;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ExpensesModel  $expense
     * @return \Illuminate\Http\Response
     */
    public function edit(ExpensesModel $expense)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ExpensesModel  $expense
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ExpensesModel $expense)
    {
        //

        $expense->expense_category_id = $request->category;
        $expense->amount = $request->amount;
        $expense->entry_date = $request->entry_date;

        $check = $expense->save();

        if($check){
            return response()->json(['message'=>'Expenses Updated'], 200);
        }
        return response()->json(['message'=>'Unable to update Expenses'], 400);        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ExpensesModel  $expense
     * @return \Illuminate\Http\Response
     */
    public function destroy(ExpensesModel $expense)
    {
        $check = $expense->delete();
        if($check){
            return response()->json(['message'=>'Expenses Deleted'], 200);
        }
        return response()->json(['message'=>'Unable to Delete Expenses'], 400);         
    }
}
