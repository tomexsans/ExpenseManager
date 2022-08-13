<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Resources\UserCollection;

use App\Http\Requests\UserPostRequest;

use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return new UserCollection( User::with(['role'=>function($query){
            $query->select('id','role_name');
        }])->paginate(10,['name','id','role_id','email','created_at as created']));
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
    public function store(UserPostRequest $request)
    {
        $validated = (object)$request->validated();
        //
        $user = new \App\Models\User;
        $user->name = $validated->name;
        $user->email = $validated->email;
        $user->role_id = $validated->role_id;
        $user->password = \Hash::make('password');
        $stat = $user->save();


        if($stat){
            return response()->json(['message'=>'User Saved'], 200);
        }
        return response()->json(['message'=>'User was not Saved'], 400);
        
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        //
        return $user;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {

        $id = $request->user->id;


        $validator = Validator::make($request->all(), [
            "name"      => "required|alpha",
            "email"     => "required|unique:users,email,{$id}",
            "role_id"   => "required|numeric"
        ]);    
        
        if ($validator->fails()) {
            return response()->json(['errors'=>$validator->messages()],422);
        }        

        $validated = $validator->validated();

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->role_id = $validated['role_id'];
        $check = $user->save();


        if($check){
            return response()->json(['message'=>'User was updated'], 200);
        }
        return response()->json(['message'=>'Error in updating'], 400);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        //
        if($user->id !== 1){
            $userDelete = $user->delete();


            if($userDelete){
                return response()->json(['message'=>'User was Deleted'], 200);
            }
            return response()->json(['message'=>'Error in Deleting User'], 422);
        }else{
            return response()->json(['message'=>'This user cannot be deleted'], 422);
        }
    }
}
