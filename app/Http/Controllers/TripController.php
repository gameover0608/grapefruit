<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Trip;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use DateTime;

class TripController extends Controller
{
    //

    public function getAllTrips() {
      // logic to get all Trips goes here
    	$Trips = Trip::get()->toJson(JSON_PRETTY_PRINT);
   		return response($Trips, 200);
    }

    public function createTrip(Request $request) {
      // logic to create a Trip record goes here
    	$Trip = new Trip;
	    $Trip->slug = Str::slug($request->title, '-') ;
	    $Trip->title = $request->title;
	    $Trip->description = $request->description;
	    $Trip->start_date = new \DateTime($request->start_date);
     	$Trip->end_date =  new \DateTime($request->end_date);
      	$Trip->location = $request->location;
       	$Trip->price =  $request->price;

	    $Trip->save();

	    return response()->json([
	        "message" => "Trip record created"
	    ], 201);
    }

    public function getTrip($slug) {
      	// logic to get a Trip record goes here

    	if (Trip::where('slug', $slug)->exists()) {
	        $Trip = Trip::where('slug', $slug)->get()->toJson(JSON_PRETTY_PRINT);
	        return response($Trip, 200);
      	} else {
	        return response()->json([
	          "message" => "Trip not found"
	        ], 404);
      	}

    }

    public function updateTrip(Request $request, $id) {
      	// logic to update a Trip record goes here

    	if (Trip::where('id', $id)->exists()) {
    		$Trip = Trip::find($id);
	        $Trip->slug = is_null($request->title) ? $Trip->slug : Str::slug($request->title, '-');
		    $Trip->title = is_null($request->title) ? $Trip->title : $request->title;
		    $Trip->description = is_null($request->description) ? $Trip->description : $request->description;
		    $Trip->start_date = is_null($request->start_date) ? $Trip->start_date :  new \DateTime($request->start_date);
	     	$Trip->end_date = is_null($request->end_date) ? $Trip->end_date :  new \DateTime($request->end_date);
	      	$Trip->location = is_null($request->location) ? $Trip->location : $request->location;
	       	$Trip->price =  is_null($request->price) ? $Trip->price : $request->price;

	        $Trip->save();

	        return response()->json([
	            "message" => "Trip updated successfully"
	        ], 200);
        } else {
	        return response()->json([
	            "message" => "Trip not found"
	        ], 404);
	        
	    }
    }

    public function deleteTrip ($id) {
      	// logic to delete a Trip record goes here
    	if(Trip::where('id', $id)->exists()) {
	        $Trip = Trip::find($id);
	        $Trip->delete();

	        return response()->json([
	          "message" => "Trip deleted"
	        ], 202);
      	} else {
	        return response()->json([
	          "message" => "Trip not found"
	        ], 404);
      	}
	}

	public function filterTrips(Request $request) {

		$collection = Trip::query();

		if(!is_null($request->search)){

			$collection->where(function ($query) use ($request) {
               $query->where('title', 'like', '%'.$request->search.'%')
                	->orWhere('location', 'like', '%'.$request->search.'%')
					->orWhere('description', 'like', '%'.$request->search.'%');
           	});

		}

		if(!is_null($request->orderBy)){

			switch ($request->orderBy) {
				case 'price-asc':
					$collection->OrderBy('price', 'asc');
					break;

				case 'price-desc':
					$collection->OrderBy('price', 'desc');
					break;

				case 'start-asc':
					$collection->OrderBy('start_date', 'asc');
					break;

				case 'start-desc':
					$collection->OrderBy('start_date', 'desc');
					break;

				case 'end-asc':
					$collection->OrderBy('end_date', 'asc');
					break;

				case 'end-desc':
					$collection->OrderBy('end_date', 'desc');
					break;
				
				default:
					# code...
					break;
			}
		}

		if(!is_null($request->price_from)){			
			$collection->where('price', '>=', $request->price_from);
		}

		if(!is_null($request->price_to)){
			$collection->where('price', '<=', $request->price_to);
		}

		
		return $collection->get();

	}
}
