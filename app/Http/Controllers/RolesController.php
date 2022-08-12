<?php

namespace App\Http\Controllers;

use App\Models\RolesModel;
use Illuminate\Http\Request;
use App\Http\Resources\RolesAll;
use App\Http\Resources\RolesAllCollection;
use Illuminate\Support\Facades\Auth;
use \App\Http\Requests\AddRole;
class RolesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return RolesModel::all(['id','role_description as description','role_name as name','created_at as created']);
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
    public function store(AddRole $request)
    {
        //validation here
        $role = new RolesModel;

        $role->role_description = $request->role_description;
        $role->role_name = $request->role_name;
        $check = $role->save();

        if($check){
            return response()->json(['message'=>'Role Added'], 200);
        }
        return response()->json(['message'=>'Unable to Add new Role'], 400);        

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\RolesModel  $role
     * @return \Illuminate\Http\Response
     */
    public function show(RolesModel $role)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\RolesModel  $role
     * @return \Illuminate\Http\Response
     */
    public function edit(RolesModel $role)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\RolesModel  $role
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, RolesModel $role)
    {

        $id = $role->id;


        $validator = \Validator::make($request->all(), [
            'role_description' => 'required',
            'role_name' => "required|unique:roles,role_name,{$id}"
        ]);            

        if ($validator->fails()) {
            return response()->json([$validator->messages()],422);
        }   

        $validated = $validator->validated();        
        //
        if($role->id != 1){
            $role->role_name = $validated['role_name'];
            $role->role_description = $validated['role_description'];
            $role->save();            

            return response()->json(['message'=>'Roles Updated'], 200);
        }else{
            return response()->json(['message'=>'This role is not allowed to be Updated'], 401);
        }
        
        return $request;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\RolesModel  $role
     * @return \Illuminate\Http\Response
     */
    public function destroy(RolesModel $role)
    {
        //
        if($role->id != 1){
            
            $result = \App\Models\User::where('role_id',$role->id)->count();

            if($result >= 1){
                return response()->json(['message'=>'Cannot Delete Role. Has existing data.'], 400);      
            }
            
            $role->delete();



            return response()->json(['message'=>'Roles Deleted'], 200);
        }else{
            return response()->json(['message'=>'This role is not allowed to be Deleted'], 401);
        }        
    }
}
