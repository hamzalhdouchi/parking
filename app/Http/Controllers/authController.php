<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;


class AuthController extends Controller
{
     /**
     * @OA\Post(
     * path="/api/register",
     * summary="Register a new user",
     * description="Register a new user with name, email, and password",
     * operationId="register",
     * tags={"Auth"},
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * required={"name", "email", "password"},
     * @OA\Property(property="name", type="string", example="John Doe"),
     * @OA\Property(property="email", type="string", format="email", example="johndoe@example.com"),
     * @OA\Property(property="password", type="string", format="password", example="securepassword123")
     * )
     * ),
     * @OA\Response(
     * response=200,
     * description="User created successfully",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="The user was created successfully")
     * )
     * ),
     * @OA\Response(
     * response=400,
     * description="Bad request"
     * )
     * )
     */
    public function register(RegisterRequest $request)
    {
        $request->validated();

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->createToken('parking')->plainTextToken;
        return response()->json(
            
            'The user was created successfully');
    }

      /**
     * @OA\Post(
     * path="/api/login",
     * summary="Login user",
     * description="Login a user with email and password",
     * operationId="login",
     * tags={"Auth"},
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * required={"email", "password"},
     * @OA\Property(property="email", type="string", format="email", example="johndoe@example.com"),
     * @OA\Property(property="password", type="string", format="password", example="securepassword123")
     * )
     * ),
     * @OA\Response(
     * response=200,
     * description="User logged in successfully",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="User logged in successfully"),
     * @OA\Property(property="token", type="string", example="your-jwt-token-here")
     * )
     * ),
     * @OA\Response(
     * response=400,
     * description="Incorrect credentials"
     * )
     * )
     */

    public function login(LoginRequest $request)
    {

        
        $request->validated();

        $user = User::where('email', $request->email)->first();
        
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(
                'The provided credentials are incorrect'
            );
        }
        $token = $user->createToken('parking')->plainTextToken;

        return response()->json([
            'message' => 'User logged in successfully',
            'token' => $token
        ], 200);
    }

    /**
     * @OA\Post(
     * path="/api/logout",
     * summary="Logout user",
     * description="Logout the current user by revoking their token",
     * operationId="logout",
     * tags={"Auth"},
     * security={{"bearerAuth": {}}},
     * @OA\Response(
     * response=200,
     * description="Logout successful",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="Logout successful")
     * )
     * )
     * )
     */

    
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json([
            'message' => 'Logout successful'
        ]);
    }
}
