<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * @OA\Info(
     *    title="Api template laravel 10",
     *    version="1.0",
     *    description="Documentation API ",
     *   @OA\Contact(
     *       name="Junior Javier Garcia Luciano",
     *       url= "https://javierluciano.com/",
     *       email = "javierluciano12345@gmail.com",
     *   ),
     * ),
     * @OA\SecurityScheme(
     *   securityScheme="token",
     *   type="http",
     *   name="Authorization",
     *   in="header",
     *   scheme="Bearer"
     * )
     */
    protected function response($data = null, $status = 200, $message = null)
    {
        $response = [
            'status' => $status >= 200 && $status < 300 ? 'success' : 'error',
            'message' => $message ?? ($status >= 200 && $status < 300 ? 'Success' : 'Error'),
        ];

        if (!is_null($data)) {
            $response['data'] = $data;
        }

        return response()->json($response, $status);
    }
}
