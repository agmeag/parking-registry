<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected function success($responseData = null, $message = null, int $statusCode = 200)
    {
        $data = is_array($responseData) ? $responseData : ($responseData ?: null);

        $meta = null;
        if ($data instanceof AnonymousResourceCollection && !is_array($data->response()->getData())) {
            $response = $data->response()->getData();
            $meta = isset($response->meta) ? $response->meta : null;
            $data = $response->data;
        }

        return response()->json(
            [
                'status' => 'success',
                'message' => $message,
                'data' => $data,
            ]
                + (!$meta ? [] : ['meta' => $meta]),
            $statusCode
        );
    }

    protected function error($exception, ?int $statusCode = 400)
    {
        $isException = $exception instanceof \Exception;
        $message = $isException ? $exception->getMessage() : $exception;

        if (!$statusCode) {
            $statusCode = method_exists($exception, 'getStatusCode') ? $exception->getStatusCode() : 500;
        }

        // Prevent SQL Errors or others to be shown in production
        if (app()->isProduction() && (is_string($message) && strlen($message) > 100)) {
            $message = 'Oops! Something went bad';
        }

        return response()->json([
            'status' => 'error',
            'message' => $message,
        ], $statusCode);
    }
}