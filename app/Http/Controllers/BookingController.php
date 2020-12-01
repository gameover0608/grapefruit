<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Trip;
use App\Booking;

class BookingController extends Controller
{
   
	public function book(Request $request){

		$validator = \Validator::make($request->all(), [ 
	        'token' => 'required',
	        'trip_id' => 'required|integer',
	    ]);
	    if ($validator->fails()) {

	        return response()->json([
	            "message" => "Token and/or Trip Id Not Set."
	        ], 400);

	    }

    	if(User::where('remember_token', hash('sha256', $request->token))->exists()){

    		if(!Trip::where('id', $request->trip_id)->exists()){

    			return response()->json([
		          "message" => "Trip not found"
		        ], 404);

    		}
    		
    		$user = User::where('remember_token', hash('sha256', $request->token))->first();

			$booking = new Booking();
			$booking->user_id = $user->id;
			$booking->trip_id = $request->trip_id;
			$booking->save();

			$yourBookings = Booking::where('user_id', $user->id)->get();

			return response()->json([
	            "message" => "Your booking has been saved.",
	            'bookings' => $yourBookings
	        ], 200);


    	} else{
    		return response()->json([
	            "message" => "Unauthorized. Please authenticate and try again."
	        ], 401);
    	}

		

	}

}
