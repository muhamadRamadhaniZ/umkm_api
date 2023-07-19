<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class UserController extends Controller
{
    public function register(Request $request) {
        try {
            $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string','email', 'max:255', 'unique:users'],
                'foto' => ['required'],
                'visi' => ['required', 'string', 'max:255'],
                'misi' => ['required', 'string', 'max:255'],
                'password' => ['required','min:8' ,'string'],
            ],); 
            $files = $request->file('foto');
            $fileName = date('YmdHi').str_replace(' ', '', $request->file('foto')->getClientOriginalName());
            $files->move(public_path('images'), $fileName);
            User::create([
                'name' => $request->name,
                'foto' => url('')."/images/".$fileName,
                'visi' => $request->visi,
                'misi' => $request->misi,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $user = User::where('email', $request->email)->first();

            // create token 
            $tokenResult = $user->createToken('authToken')->plainTextToken;

            return ResponseFormatter::success([
                'access_token' => $tokenResult,
                'token_type' => 'Bearer',
                'user' => $user
            ], 'User Registered');
        } catch (Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something went wrong',
                'error' => $error->errors()
            ], 'Authentication Failed', 500);
        }
    }
    public function login(Request $request) {
        try{
            $request->validate([
                'email' => 'email|required',
                'password' => 'required',
            ]);

            $creadentials = request(['email', 'password']);

            if(!Auth::attempt($creadentials)){
                 return ResponseFormatter::error([
                    'message' => 'Unauthorized'
                 ], 'Authentication Failed', 500);
            }

            $user = User::where('email', $request->email)->first();

            if(! Hash::check($request->password, $user->password, [])){
                 throw new \Exception('Invalid Credentilas');
            }

            $tokenResult = $user->createToken('authToken')->plainTextToken;
            return ResponseFormatter::success([
                'access_token' => $tokenResult,
                'token_type' => 'Bearer',
                'user' => $user
            ], 'Authenticated');
        } catch(Exception $error){
            return ResponseFormatter::error([
                'message' => 'Something went wrong',
                'error' => $error
            ], 'Authentication Failed', 500 );
        }
    }

    public function update(Request $request) {
        try {
            $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string','email', 'max:255'],
                'visi' => ['required', 'string', 'max:255'],
                'misi' => ['required', 'string', 'max:255'],
            ],); 
            $user = User::find(auth()->user()->id);
            $fileName = '';
            if ($request->file('foto')) {
                $files = $request->file('foto');
                $fileName = date('YmdHi').str_replace(' ', '', $request->file('foto')->getClientOriginalName());
                $files->move(public_path('images'), $fileName);
            }
            $password = $request->password ? Hash::make($request->password) : '';
            $user->name = $request->name ? $request->name : $user->name;
            $user->email = $request->email ? $request->email : $user->email;
            $user->visi = $request->visi ? $request->visi : $user->visi;
            $user->misi = $request->misi ? $request->misi : $user->misi;
            $user->foto = $fileName ? url('')."/images/".$fileName : $user->foto;
            $user->password = $password ? $password : $user->password;
            $user->save();

            $user = User::where('email', $request->email)->first();

            // create token 
            $tokenResult = $user->createToken('authToken')->plainTextToken;

            return ResponseFormatter::success([
                'access_token' => $tokenResult,
                'token_type' => 'Bearer',
                'user' => $user
            ], 'User Updated');
        } catch (Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something went wrong',
                'error' => $error->errors(),
            ], 'User fail updated', 500 );
        }
    }
    public function fetch(Request $request){
        return ResponseFormatter::success($request->user(), 'Data user berhasil diambil');
    }
    public function logout(Request $request){
        $token = $request->user()->currentAccessToken()->delete();
        return ResponseFormatter::success($token, 'Token Revoked');
    }
}
