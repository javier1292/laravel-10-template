<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/v1/profile/avatar",
     *     summary="Upload avatar",
     *     description="Upload or update avatar of te user.",
     *     tags={"Profile"},
     *     security={{ "token": {} }},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                     property="avatar",
     *                     description="image to upload",
     *                     type="string",
     *                     format="binary"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Avatar upload or update successfully.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="avatar", type="string")
     *         )
     *     )
     * )
     */
    public function uploadAvatar(Request $request)
    {

        $user = auth()->user();

        if ($user->profile->avatar) {
            Storage::delete('avatars/' . basename($user->profile->avatar));
        }
        $file = $request->file('avatar');
        $path = $file->storeAs('avatars', $file->hashName());

        $url = Storage::url($path);

        $user->profile()->update(['avatar' => $url]);

        return $this->response([
            'avatar' => $url
        ], 200, 'Profile updated successfully');
    }
}
