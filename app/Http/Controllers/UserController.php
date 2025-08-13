<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $data = [
            'pageTitle' => "User",
            'users' => User::where('role', 'user')->get()
        ];
        return view('users.index', $data);
    }

    public function deactivate(Request $r){
        $validatedData = $r->validate([
            'id' => 'required|string',
        ], [
            'id.required' => 'Data belum dipilih.',
            'id.string' => 'Data belum dipilih.',
        ]);

        try{
            $user = User::where('id', $r->id)
                            ->first();

            if($user->status == 1){
                $user->status = 0;
                $user->save();
            }else{
                $user->status = 1;
                $user->save(); 
            }

            return response()->json([
                'status' => true,
                'data' => $user
            ]);
        }catch(Exception $e){
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}
