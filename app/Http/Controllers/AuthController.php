<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        try {
            $credentials = $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);

            // Cek user di database
            $user = \App\Models\User::where('email', $credentials['email'])->first();

            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'Email tidak ditemukan.'
                ]);
            }

            if ($user->status != 1) {
                return response()->json([
                    'status' => false,
                    'message' => 'Akun Anda belum aktif.'
                ]);
            }

            // Proses login
            if (auth()->attempt($credentials)) {
                return response()->json([
                    'status' => true,
                    'role' => $user->role,
                ]);
            }

            return response()->json([
                'status' => false,
                'message' => 'Password salah.'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }


    public function register(Request $request)
    {
        try {
            $data = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:3',
                'password_confirmation' => 'required|string|same:password',
                'file_identitas' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5048',
            ]);

            if ($request->hasFile('file_identitas')) {
                $file = $request->file('file_identitas');
                $path = $file->store('file_identitas', 'public');
                $data['file_identitas'] = $path;
            }

            $data['password'] = bcrypt($data['password']);
            $data['role'] = 'user';
            unset($data['password_confirmation']);

            $user = \App\Models\User::create($data);

            auth()->login($user);

            return response()->json([
                'status' => true,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan pada proses pendaftaran. Silakan coba lagi.'
            ]);
        }
    }

    public function logout()
    {
        try {
            auth()->logout();
            return redirect('/');
        } catch (\Exception $e) {
            return back()->withErrors([
                'error' => 'Terjadi kesalahan pada proses logout. Silakan coba lagi.'
            ]);
        }
    }
}
