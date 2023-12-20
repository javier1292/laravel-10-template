<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\ForgotPasswordNotify;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VerificationController extends Controller
{
    public function verify($id, $hash)
    {
        $user = User::findOrFail($id);

        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }

        return 'Your email has been verified.';
    }

    /**
     * @OA\Post(
     *     path="/api/v1/forgot-password",
     *     summary="Forgot password",
     *     description="Send an email to change the password.",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="email", type="string", format="email")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Email send successfully."
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid data."
     *     )
     * )
     */
    public function forgotPassword(Request $request)
    {

        $request->validate([
            'email' => 'required|email'
        ]);

        $user = User::where('email', $request->email)->first('email');

        $token = bin2hex(random_bytes(32));

        if (DB::table('password_reset_tokens')->where('email', $request->email)->exists()) {
            DB::table('password_reset_tokens')->where('email', $request->email)->update([
                'token' => $token
            ]);

            $user->notify(new ForgotPasswordNotify($token));
            return $this->response([
                'message' => 'Email sent'
            ], 200);
        }

        DB::table('password_reset_tokens')->insert([
            'email' => $request->email,
            'token' => $token
        ]);


        $user->notify(new ForgotPasswordNotify($token));

        return $this->response([
            'message' => 'Email sent'
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/reset-password",
     *     summary="Reset password",
     *     description="Reset password of the user.",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="email", type="string", format="email"),
     *             @OA\Property(property="token", type="string"),
     *             @OA\Property(property="password", type="string", format="password"),
     *             @OA\Property(property="password_confirmation", type="string", format="password")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Password reset successfully."
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid token or data."
     *     )
     * )
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users',
            'token' => 'required',
            'password' => 'required|confirmed|min:6'
        ]);

        $token = DB::table('password_reset_tokens')->where('email', $request->email)->value('token');

        if (!$token) {
            return $this->response([
                'message' => 'Invalid token'
            ], 400);
        }

        if ($token !== $request->token) {
            return $this->response([
                'message' => 'Invalid token'
            ], 400);
        }

        $user = User::where('email', $request->email)->first('password', 'email');

        $user->password = bcrypt($request->password);
        $user->save();

        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return $this->response([
            'status' => 'success',
            'message' => 'Password reset successfully'
        ], 200);
    }
}
