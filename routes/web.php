<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

Route::get('/', function () {
    return csrf_token();
});

Route::post('/data', function (Request $request) {
    $name = $request->input('name');
    $email = $request->input('email');
    $password = Hash::make($request->input('password')); // Hashing the password before storing
    $user = User::create([
        'name' => $name,
        'email' => $email,
        'password' => $password
    ]);
    return "Created user with email: " . $email;
});

Route::get('/data', function () {
    $users = User::all();
    return $users;
});

Route::post('/login', function (Request $request) {
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    $user = User::where('email', $request->email)->first();

    if (!$user || !Hash::check($request->password, $user->password)) {
        throw ValidationException::withMessages([
            'email' => ['The provided credentials are incorrect.'],
        ]);
    }

    return response()->json([
        'message' => 'Login successful',
        'user' => $user
    ]);
});

Route::put('/data/{email}', function ($email, Request $request) {
    $user = User::where('email', $email)->first();

    // Memeriksa apakah email ditemukan
    if ($user) {
        $user->name = $request->input('name', $user->name);
        $user->email = $request->input('email', $user->email);

        // Jika password diubah, hash password baru
        if ($request->has('password')) {
            $user->password = bcrypt($request->input('password'));
        }

        // Simpan perubahan
        $user->save();

        return response()->json(['message' => 'User updated successfully', 'user' => $user], 200);
    } else {
        return response()->json(['message' => 'Email tidak ditemukan'], 404);
    }
});

Route::patch('/data/{email}', function ($email, Request $request) {
    // Temukan user berdasarkan email
    $user = User::where('email', $email)->first();

    // Memeriksa apakah email ditemukan
    if ($user) {
        // Update data user berdasarkan request body
        if ($request->has('name')) {
            $user->name = $request->input('name');
        }
        // Hanya update email jika ada perubahan
        if ($request->has('email')) {
            $user->email = $request->input('email');
        }
        // Jika password diubah, hash password baru
        if ($request->has('password')) {
            $user->password = bcrypt($request->input('password'));
        }

        // Simpan perubahan
        $user->save();

        return response()->json(['message' => 'User modified successfully', 'user' => $user], 200);
    } else {
        return response()->json(['message' => 'Email tidak ditemukan'], 404);
    }
});

Route::delete('/data/{email}', function (Request $request, $email) {
    DB::table('users')->where('email', $email)->delete();
    return response()->json([
        'message' => 'User with email ' . $email . ' deleted'
    ]);
});
