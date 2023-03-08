<?php

namespace App\Http\Controllers\Api;

use App\Models\Favorite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FavoritesController extends \App\Http\Controllers\Controller
{

    public function init(Request $request) 
    {
        // Log::info("user: " . $request->user()->id);
        // Log::info("action: " . $request->input('action'));
        Log::info("request: " . print_r($request->input(),true));

        if($request->input('action') == 'create')
        {
            if( Favorite::create([
                
                'user_id' => $request->user()->id,
                'article_id_fk' => $request->input('id'),
            
            ]) )
            {
                return response()->json([
                    'success'=>'Favorite created successfully',
                ], 200);
    
            } else {
                
                return response()->json([
                    'error' => 'Error creating favorite'
                ], 500);
            }

        } elseif($request->input('action') == 'delete') 
        {
            if( Favorite::where('article_id_fk',$request->input('id'))->
                    where('user_id',$request->user()->id)->
                    delete() )
            {
                return response()->json([
                    'success'=>'Favorite deleted successfully',
                ], 200);

            } else {
                
                return response()->json([
                    'error' => 'Error deleting favorite'
                ], 500);
            }
        } elseif($request->input('action') == 'deleteAll') 
        {
            if( Favorite::where('user_id',$request->user()->id)->
                    delete() )
            {
                return response()->json([
                    'success'=>'Favorites deleted successfully',
                ], 200);

            } else {
                
                return response()->json([
                    'error' => 'Error deleting favorites'
                ], 500);
            }
        }
        
    }

   
    // public function remove(Request $request) {
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

    //     if($user->tokens()->delete())
    //     {
    //         return response()->json([
    //             'success' => 'User logged out successfully'
    //         ], 200);

    //     } else {
    //         return response()->json([
    //             'error' => $validator->errors()
    //         ], 401);
    //     }

    // }


   
}
