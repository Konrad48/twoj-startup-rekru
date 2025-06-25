<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    /**
     * Register a new user.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     * 
     * @response array{user: User, token: string}
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8'
        ]);

        $validated['password'] = Hash::make($validated['password']);
        
        $user = User::create($validated);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token
        ], 201);
    }

    /**
     * Get currently authenticated user.
     *
     * @return \App\Models\User
     * 
     * @response User
     */
    public function show()
    {
        return response()->json(Auth::user());
    }

    /**
     * Log in a user.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     * 
     * @response array{user: User, token: string}
     */
    public function login(Request $request)
    {
        $fields = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $fields['email'])->first();

        if (!$user || !Hash::check($fields['password'], $user->password)) {
            return response([
                'message' => 'Bad credits'
            ], 401);
        }

        return response()->json([
            'user' => $user,
            'token' => $user->createToken('auth_token')->plainTextToken
        ]);
    }

    /**
     * Update currently authenticated user.
     *
     * @param \Illuminate\Http\Request $request
     * @return \App\Models\User
     * 
     * @response User
     */
    public function update(Request $request)
    {

        $validated = $request->validate([
            'first_name' => 'string|max:255',
            'last_name' => 'string|max:255',
            'phone_number' => 'string|max:20',
        ]);
        $user = Auth::user();
        $user->update($validated);
        return $user;
    }

    /**
     * Delete currently authenticated user.
     *
     * @param \App\Models\User $user
     * @return \Illuminate\Http\Response
     * 
     * @response 204 No Content
     */
    public function destroy()
    {
        Auth::user()->delete();
        return response()->noContent();
    }

    
}