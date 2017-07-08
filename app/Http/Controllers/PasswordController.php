<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

use App\User;

class PasswordController extends Controller{
  public function show(){
    return view('change_password')->with('user',Auth::user());
  }

  public function update(Request $request , $id){
  	$this->validate($request, [
        'old_password' => 'required|max:100|min:6',
        'new_password' => 'required|max:100|min:6',
        'confirm_password' => 'required|max:100|same:new_password|min:6'
    ]);

    $user = User::findOrFail($id);

    if (Hash::check($request->input('old_password'), $user->getAuthPassword())) {
      $user->password = Hash::make($request->input('new_password')) ;
      $user->save() ;
    }else{
      return redirect('/change-password')->with('errors', collect('Incorrect password') );
    }


    return redirect('/')->with('status', 'Success new password!');
  }
}