<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/



Route::post('authenticate', [ 'as' => 'authenticate', 'uses' => 'UserController@loginUser']);

Route::get('users/getall', 'UserController@getAllUsers');
Route::get('users/get/{id}', 'UserController@getUser');
Route::post('users/create', 'UserController@createUser');
Route::put('users/update/{id}', 'UserController@updateUser');
Route::delete('users/delete/{id}','UserController@deleteUser');

Route::get('trips/getall', 'TripController@getAllTrips');
Route::get('trips/get/{slug}', 'TripController@getTrip');
Route::post('trips/create', 'TripController@createTrip');
Route::put('trips/update/{id}', 'TripController@updateTrip');
Route::delete('trips/delete/{id}','TripController@deleteTrip');
Route::get('trips/filter','TripController@filterTrips');

Route::post('book/create', 'BookingController@book');

