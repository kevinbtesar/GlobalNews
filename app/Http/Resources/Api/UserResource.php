<?php
namespace App\Http\Resources\Api;

use App\User;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function login(Request $request)
    {
        $this->validate($request, [
             'email' => 'required',
            'password' => 'required',
        ]);

        $email = $request->email;
        $password = $request->password;

        $user = User::where('email', $email)->where('password', $password)->first();
        if($user) {
            $success['token'] =  $user->createToken('myapp')-> accessToken;
            $success['user'] = UserResource($user);
                return response()->json(['success' => $success], 200);
            }
        return response()->json(['error' => 'UnAuthorised'], 401);

    }
}
