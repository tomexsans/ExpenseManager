<?php

namespace App\Http\Controllers;

use App\Models\ExpenseCategoryModel;
use Illuminate\Http\Request;
use App\Http\Requests\AddCategoryRequest;

class ExpensesCategory extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return ExpenseCategoryModel::paginate(12,['id','category_name as name','category_description as desc','created_at as created']);
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
    public function store(AddCategoryRequest $request)
    {
        //
        $request->validated();

        $cat = new ExpenseCategoryModel;
        $cat->category_name = $request->name;
        $cat->category_description = $request->desc;
        $res = $cat->save();

        if($res){
            return response()->json(['message'=>'Category Saved'], 200);
        }
        return response()->json(['message'=>'Category was not Saved'], 400);
                
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ExpenseCategoryModel  $expenseCategoryModel
     * @return \Illuminate\Http\Response
     */
    public function show(ExpenseCategoryModel $expensescat)
    {
        //
        return $expensescat;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ExpenseCategoryModel  $expenseCategoryModel
     * @return \Illuminate\Http\Response
     */
    public function edit(ExpenseCategoryModel $expenseCategoryModel)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ExpenseCategoryModel  $expenseCategoryModel
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ExpenseCategoryModel $expensescat)
    {
        //
        $id = $request->id;


        $validator = \Validator::make($request->all(), [
            'name'=>"required|unique:expense_category,category_name,{$id}",
            'desc'=>'required'
        ]);            

        if ($validator->fails()) {
            return response()->json($validator->messages(),422);
        }   

        $validated = $validator->validated();

        $expensescat->category_name = $validated['name'];
        $expensescat->category_description = $validated['desc'];        
        $check = $expensescat->save();


        if($check){
            return response()->json(['message'=>'Category was updated'], 200);
        }
        return response()->json(['message'=>'Error in updating'], 400);        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ExpenseCategoryModel  $expenseCategoryModel
     * @return \Illuminate\Http\Response
     */
    public function destroy(ExpenseCategoryModel $expensescat)
    {
        //

        $result = \App\Models\ExpensesModel::where('expense_category_id',$expensescat->id)->count();

        if($result >= 1){
            return response()->json(['message'=>'Cannot Delete Category. Has existing data.'], 400);      
        }

        $check = $expensescat->delete();

        if($check){
            return response()->json(['message'=>'Category was Deleted'], 200);
        }
        return response()->json(['message'=>'Error in Deleting'], 400);      
    }
}
