<?php

namespace App\Http\Controllers\Api;

use App\Models\Favorite;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;


class AuthController extends \App\Http\Controllers\Controller
{

    public function init(Request $request)
    {
        if($request->input('userAuth') == 'true'){
            return self::userAuthGranted($request);
        } else {
            return self::userAuthNotGranted($request);
        }
    }

    public function userAuthGranted(Request $request)
    {
        try {

            $validator  = Validator::make($request->all(), [
                'email' => 'required|string|email',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'error' => $validator->errors()
                ], 400);
            }

            // Retrieve the validated input...
            $validated = $validator->validated();

            // Check email
            $user = User::where('email', $validated['email'])->first();

            if ($user) 
            {
                // Revoke all tokens...
                $user->tokens()->delete();
                // get user favorites
                // $favorites = Favorite::where('user_id', $user->id)->get();
                $favorites = Favorite::where('user_id', $user->id)
                    ->with('articleData')
                    ->get();

                return response()->json([
                    'success' => 'User logged in successfully',
                    'user' => $user,
                    'accessToken' => $user->createToken($request->deviceId ?? 'unknownDeviceId')->plainTextToken,
                    'favorites' => $favorites,
                ], 200);

            } else {
                $validator = Validator::make($request->all(), [
                    'email' => 'required|string|unique:users,email',
                    'name' => 'required|string',
                    'appId' => 'string',
                ]);

                if ($validator->fails()) {
                    return response()->json([
                        'error' => $validator->errors()
                    ], 401);
                }

                // Retrieve the validated input...
                $validated = $validator->validated();

                // $user = null;
                if ($user = $this->createUser($validated)) 
                {
                    return response()->json([
                        'success' => 'User created successfully',
                        'user' => $user,
                        'accessToken' => $user->createToken($request->deviceId ?? 'unknownDeviceId')->plainTextToken,
                    ], 201);

                } else {
                    return response()->json(['error', 'AuthController userAuthGranted() createUser() failed'], 500);
                }
            }
        } catch (\Exception $ex) {
            return response()->json(['error', 'AuthController userAuthGranted() Error Message: ' . $ex->getMessage()], 500);
        }
    }


    private function createUser($validated)
    {
        if ($user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password'] ?? ''),
            'app_id' => $validated['appId'],

        ])) {
            return $user;
        } else {
            return false;
        }
    }


    public function userAuthNotGranted($request)
    {
        $validator  = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);


        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()
            ], 400);
        }

        // Retrieve the validated input...
        $validated = $validator->validated();

        // Check email
        $user = User::where('email', $validated['email'])->first();
        //         $userT = User::where('email', $validated['email'])->toSql();
        // Log::info('user: ' . $userT);

        if ($user) {

            // Revoke all tokens...
            $user->tokens()->delete();
            
            // Check password
            if (!Hash::check($validated['password'], $user->password)) {
                return response()->json([
                    'error' => 'Your email did not match your password'
                ], 401);
            } else {
                
                return response()->json([
                    'success' => 'User logged in successfully',
                    'user' => $user,
                    'accessToken' => $user->createToken($request->deviceId ?? 'unknownDeviceId')->plainTextToken,
                ], 200);
            }
        } else {

            $validator = Validator::make($request->all(), [
                'name' => 'required|string',
                'email' => 'required|string|unique:users,email',
                'password' => 'required|string'
                // 'password' => 'required|string|confirmed'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'error' => $validator->errors()
                ], 400);
            }

            // Retrieve the validated input...
            $validated = $validator->validated();

            if ($user = $this->createUser($validated)) 
            {
                return response()->json([
                    'success' => 'User created successfully',
                    'user' => $user,
                    'accessToken' => $user->createToken($request->device_name ?? 'myapptoken')->plainTextToken
                ], 201);

            } else {
                return response()->json(['error', 'AuthController userAuthNotGranted() createUser() failed'], 500);
            }
        }

    }


    // public function logout(Request $request)
    // {
    //     $validator  = Validator::make($request->all(), [
    //         'email' => 'required|string|email',
    //     ]);


    //     if ($validator->fails()) {
    //         return response()->json([
    //             'error' => $validator->errors()
    //         ], 401);
    //     }
    //     $validated = $validator->validated();

    //     $user = User::where('email', $validated['email'])->first();

    //     if ($user->tokens()->delete()) {
    //         return response()->json([
    //             'success' => 'User logged out successfully'
    //         ], 200);
    //     } else {
    //         return response()->json([
    //             'error' => $validator->errors()
    //         ], 401);
    //     }
    // }


    // public function register(Request $request) {
    //     $fields = $request->validate([
    //         'name' => 'required|string',
    //         'email' => 'required|string|unique:users,email',
    //         'password' => 'required|string|confirmed'
    //     ]);

    //     $user = User::create([
    //         'name' => $fields['name'],
    //         'email' => $fields['email'],
    //         'password' => bcrypt($fields['password'])
    //     ]);

    //     $token = $user->createToken('myapptoken')->plainTextToken;

    //     $response = [
    //         'user' => $user,
    //         'token' => $token
    //     ];

    //     return response($response, 201);
    // }

    // public function login(Request $request) {
    //     $fields = $request->validate([
    //         'email' => 'required|string',
    //         'password' => 'required|string'
    //     ]);

    //     // Check email
    //     $user = User::where('email', $fields['email'])->first();

    //     // Check password
    //     if(!$user || !Hash::check($fields['password'], $user->password)) {
    //         return response([
    //             'message' => 'Bad creds'
    //         ], 401);
    //     }

    //     $token = $user->createToken('myapptoken')->plainTextToken;

    //     $response = [
    //         'user' => $user,
    //         'token' => $token
    //     ];

    //     return response($response, 201);
    // }


}
