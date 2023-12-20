<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use App\Models\Role;

class AuthController extends Controller
{
    protected function getDeviceName(Request $request)
    {
        $deviceName = $request->device_name ?? $request->header('User-Agent') ?? 'unknown';

        return $deviceName;
    }

    /**
     * @OA\Post(
     *     path="/api/v1/register",
     *     summary="User register",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="email", type="string", format="email"),
     *             @OA\Property(property="password", type="string", format="password"),
     *             @OA\Property(property="first_name", type="string"),
     *             @OA\Property(property="last_name", type="string"),
     *             @OA\Property(property="user_type", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User registered successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="token", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="invalid data"
     *     )
     * )
     */
    public function register(Request $request)
    {
        $role = Role::findByName($request->user_type);

        $user = User::create([
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'role_id' => $role->id,
        ]);

        $user->profile()->create();
        $user->assignRole($role);
        $user->sendEmailVerificationNotification();
        $token = $user->createToken($this->getDeviceName($request));

        return $this->response([
            'user' => new UserResource($user),
            'token' => $token->plainTextToken,
        ], 201, 'User registered successfully');
    }

    /**
     * @OA\Post(
     *     path="/api/v1/login",
     *     summary="Login",
     *     tags={"Authentication"},
     *     description="Authentication user and token create.",
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="email", type="string", format="email"),
     *             @OA\Property(property="password", type="string", format="password")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User authenticated successfully.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="token", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Invalid credentials."
     *     )
     * )
     */
    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $data['email'])->first(['id', 'password', 'email_verified_at', 'status']);

        if (!$user || !Hash::check($data['password'], $user->password)) {
            return $this->response(null, Response::HTTP_UNAUTHORIZED, 'Invalid credentials');
        }

        if ($user->status == 'inactive') {
            return $this->response(null, Response::HTTP_UNAUTHORIZED, 'User is inactive');
        }

        $token = $user->createToken($this->getDeviceName($request));

        return response()->json([
            'message' => 'User logged in successfully',
            'token' => $token->plainTextToken,
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/logout",
     *     summary="Logout",
     *     tags={"Authentication"},
     *     description="Erase access token of the user to logout.",
     *     security={{ "token": {} }},
     *     @OA\Response(
     *         response=200,
     *         description="User logout successfully."
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Logout error."
     *     )
     * )
     */
    public function logout(Request $request)
    {

        $model = Sanctum::$personalAccessTokenModel;

        $token = $request->bearerToken();
        $accessToken = $model::findToken($token);
        $deleted = $accessToken->delete();

        if (!$deleted) {
            return $this->response(null, Response::HTTP_BAD_REQUEST, 'Unable to logout');
        }

        return $this->response(null, Response::HTTP_OK, 'User logged out successfully');
    }

    /**
     * @OA\Put(
     *     path="/api/v1/profile/password",
     *     summary="Password changed",
     *     tags={"Authentication"},
     *     description="Change the password user.",
     *     security={{ "token": {} }},
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="current_password", type="string", format="password"),
     *             @OA\Property(property="new_password", type="string", format="password")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Password changed successfully."
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Error the current password or new password is not valid."
     *     )
     * )
     */
    public function changePassword(Request $request)
    {
        $data = $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string',
        ]);

        $user = auth()->user();

        if (!Hash::check($data['current_password'], $user->password)) {
            return $this->response(null, Response::HTTP_UNAUTHORIZED, 'Invalid current password');
        }

        if ($data['current_password'] == $data['new_password']) {
            return $this->response(null, Response::HTTP_UNAUTHORIZED, 'New password cannot be same as old password');
        }

        $user->password = Hash::make($data['new_password']);
        $user->save();

        return $this->response(null, Response::HTTP_OK, 'Password changed successfully');
    }
}
