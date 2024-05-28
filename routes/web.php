<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Models\User;

Route::get('/', function () {
    return csrf_token();
});

Route::post('/data', function (Request $request) {
    $name=$request->input('name');
    $email=$request->input('email');
    $password=$request->input('password');
    $user=User::create([
        'name'=>$name,
        'email'=>$email,
        'password'=>$password
    ]);
    return "Created user with email: ".$email;
});
    
Route::get('/data', function () {
    $users = User::all();
    $password = DB::table('users')->select('password')->get();
    return $users;
});

Route::put('/data', function () {
    return "This is my first update";
});

Route::patch('/data', function () {
    return "This is my first modified";
});

Route::delete('/data/{email}', function (Request $request, $email) {
    $email=$request->email;
    DB::table('users')->where('email', $email)->delete();
    return response()->json([
        'message'=>'Users with email '.$email.' deleted'
    ]);
});

