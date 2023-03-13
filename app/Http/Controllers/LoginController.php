<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use DB;

class LoginController extends Controller
{
    
    public function login(){
        return view("login");
    }

    public function sign_in (Request $request){

    	$data = $request->all();
    	$username = $data["username"];
    	$password =  $data["password"];
    	$request->validate([
    		"username" => "required",
    		"password" => "required"
    	]);

    	$user_data = DB::select("SELECT * FROM users_tbl WHERE username = '$username'");
    	if($user_data){
            if($user_data[0]->password == $password){
                session(['user_data' => $user_data[0]]);
				session('user_data')->preview_format =  1;
                return redirect()->route("main");
            }
    	}
    	throw ValidationException::withMessages(['invalid_login' => 'Invalid login. Credential submitted is unauthorized!']);

    }
}
