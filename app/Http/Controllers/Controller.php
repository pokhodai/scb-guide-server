<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * success response method.
     *
     * @return JsonResponse
     */
    public function sendResponse($result, $name): JsonResponse
    {
        $response = [
            'result' => 'success',
            $name => $result,
        ];
        return response()->json($response, 200);
    }

    /**
     * return error response.
     *
     * @return JsonResponse
     */
    public function sendError($error, $errorMessages = [], $code = 404): JsonResponse
    {
        $response = [
            'result' => 'error',
            'error' => $error,
        ];
        if(!empty($errorMessages)){
            $response['data'] = $errorMessages;
        }
        return response()->json($response, $code);
    }

    public function sendSuccess($success, $code = 200): JsonResponse
    {
        $response = [
            'result' => 'success',
            'success' => $success
        ];
        return response()->json($response, $code);
    }
}
