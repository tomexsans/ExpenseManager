<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ChangePassword extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $messages = [
            'confirmed' => 'The Password and Confirm Password does not match',
        ];
        $validator = \Validator::make($request->all(),[
            'oldpassword' => 'required',
            'newpassword' => 'required|confirmed|min:8',
        ],$messages);

 
        if ($validator->fails()) {
            return response()->json(['errors'=>$validator->messages()],422);                        
        }        

        //  for the old password we can create a guard to check the current password
        //  and leverage our validator class
        //  for this i will just use the Auth and hash
        if(!\Hash::check($request->oldpassword,\Auth::user()->password))
        {
            return response()->json(['errors'=>['oldpassword'=>(object)['The Password you have entered is wrong.']]],422);
        }

        $user = \App\Models\User::find( \Auth::user()->id );
        $clean = $validator->validated();

        $user->password = \Hash::make($clean['newpassword']);
        $check = $user->save();

        if($check){
            return response()->json(['message'=>'Password Updated, please login again']);            
        }
        return response()->json(['message'=>'Error has occured while saving new password.'],422);


    }
}
