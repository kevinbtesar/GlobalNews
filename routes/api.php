<?php

use App\Http\Controllers\Api\ArticlesController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\FavoritesController;
use App\Http\Controllers\Api\RedditController;
use App\Http\Resources\Api\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
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


// Public routes

Route::post('/userAuth', [AuthController::class, 'init']);
Route::get('/getArticles', [ArticlesController::class, 'getArticles']);


// Protected routes
Route::group(['middleware' => ['auth:sanctum']], function () 
{
    Route::post('/favorites', [FavoritesController::class, 'init']);

    // Route::post('/logout', [AuthController::class, 'logout']);
    // Route::get('/user', function (Request $request) {
    //     return $request->user();
    // });
});





Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


Route::get('/user/{id}', function ($id) {
    return new UserResource(User::findOrFail($id));
});

Route::get('/users', function () {
    return UserResource::collection(User::all());

});

Route::post('/sanctum/token', function (Request $request) {
    // Only authenticated users may access this route...

    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
        'device_name' => 'required',
    ]);
 
    $user = User::where('email', $request->email)->first();
 
    if (! $user || ! Hash::check($request->password, $user->password)) {
        throw ValidationException::withMessages([
            'email' => ['The provided credentials are incorrect.'],
        ]);
    }
 
    return $user->createToken($request->device_name);
});



Route::get('/profile', function () {
    return UserResource::collection(User::all());
})->middleware('guest');

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();

});


Route::post('/tokens/create', function (Request $request) {
    $user = User::findOrFail($request->id);
 
    return $user->createToken('token-name', ['server:update'])->plainTextToken;
});