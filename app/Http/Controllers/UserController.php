<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\User;
use App\LoginFail;

class UserController extends Controller
{

	public function loginUser(Request $request) {

		$credentials = request()->only(['email', 'password']);
		$token = Str::random(60);
		//var_dump(Auth::attempt($credentials, $token)); exit();

		if(Auth::attempt($credentials, $token)){

			$currentuser = User::where('email',$credentials['email'])-> first();

			$request->user()->forceFill([
	            'remember_token' => hash('sha256', $token),
	        ])->save();

			return response()->json([
	          	"message" =>  'Succesfully logged in! Your token: '.$token,
	          	"user" => Auth::user()
	        ], 201);

		} else {

			$userIp = $request->ip();

			$attemptsLastMin = $this->check_nr_attempts($userIp);

			if($attemptsLastMin >= 5){
				return response()->json([
		          	"message" => 'Too many attempts last minute.',
		        ], 429);
			}

			$loginFail = new LoginFail();
			$loginFail->user_ip = $userIp;
			$loginFail->failed_at = new \DateTime();
			$loginFail->save();

			return response()->json([
	          	"message" => 'Wrong login credentials.',
	        ], 401);
		}


	}

	private function check_nr_attempts($Ip){

		$now = new \DateTime();
		$maxtime_timestamp = $now->getTimestamp ( ) - 60;
		$maxtime_date = new \DateTime();
		$maxtime_date->setTimestamp($maxtime_timestamp);
		
		$loginFail = LoginFail::where('failed_at', '>=', $maxtime_date)->get();

		return count($loginFail);

	}

    public function getAllUsers() {
    	$users = User::get()->toJson(JSON_PRETTY_PRINT);
   		return response($users, 200);
    }

    public function createUser(Request $request) {
    	$user = new User;

    	if(!empty(User::where('email', $request->email)->first())){
    		return response()->json([
		        "message" => "Email already exists"
		    ], 409);
    	}

	    $user->first_name = $request->first_name;
	    $user->last_name = $request->last_name;
	    $user->email = $request->email;
	    $user->password = Hash::make($request->password);
	    $user->save();

	    return response()->json([
	        "message" => "User record created"
	    ], 201);
    }

    public function getUser($id) {

    	if (User::where('id', $id)->exists()) {
	        $user = User::where('id', $id)->get()->toJson(JSON_PRETTY_PRINT);
	        return response($user, 200);
      	} else {
	        return response()->json([
	          "message" => "User not found"
	        ], 404);
      	}

    }

    public function updateUser(Request $request, $id) {

    	if (User::where('id', $id)->exists()) {
	        $user = User::find($id);
	        $user->first_name = is_null($request->first_name) ? $user->first_name : $request->first_name;
	        $user->last_name = is_null($request->last_name) ? $user->last_name : $request->last_name;
	        $user->email = is_null($request->email) ? $user->email : $request->email;
	        $user->password = is_null($request->password) ? $user->password : Hash::make($request->password);
	        $user->save();

	        return response()->json([
	            "message" => "User updated successfully"
	        ], 200);
        } else {
	        return response()->json([
	            "message" => "User not found"
	        ], 404);
	        
	    }
    }

    public function deleteUser ($id) {
    	if(User::where('id', $id)->exists()) {
	        $user = User::find($id);
	        $user->delete();

	        return response()->json([
	          "message" => "User deleted"
	        ], 202);
      	} else {
	        return response()->json([
	          "message" => "User not found"
	        ], 404);
      	}
	}
}
