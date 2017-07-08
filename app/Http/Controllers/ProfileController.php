<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\User;

class ProfileController extends Controller{
  public function show(){
    return view('profile')->with('user',Auth::user());
  }

  public function update(Request $request , $id){
  	$this->validate($request, [
        'name' => 'required|max:100',
        'email' => 'required|email|unique:users,id,'.$id
    ]);

    $user = User::findOrFail($id);
    $user->name = $request->input('name');
    $user->email = $request->input('email');
    $user->save();

    return redirect('/')->with('status', 'Success save profile!');
  }
}