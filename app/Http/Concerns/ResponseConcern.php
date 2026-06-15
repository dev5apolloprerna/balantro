<?php

namespace App\Http\Concerns;

use App\Http\Constants\HttpStatus;


trait ResponseConcern
{
    // Status Codes
    const HTTP_STATUS_CODE_200 = 200; // success response
    const HTTP_STATUS_CODE_201 = 201; // Already Exists
    const HTTP_STATUS_CODE_400 = 400; // Bad Request(<name of parameter> is missing. & <name of parameter> is not a valid data to process.)
    const HTTP_STATUS_CODE_401 = 401; // Un-authorized request
    const HTTP_STATUS_CODE_403 = 403; // Already exists or Exception found error
    const HTTP_STATUS_CODE_405 = 405; // Method Not Allowed
    const HTTP_STATUS_CODE_411 = 411; // Email and Password wrong
    const HTTP_STATUS_CODE_422 = 422; // Record Not Found
    const HTTP_STATUS_CODE_433 = 433; // User not verified
    const HTTP_STATUS_CODE_617 = 617; // Validation failed (Record Invalid)
    const HTTP_STATUS_CODE_618 = 618; // password not matched
    const HTTP_STATUS_CODE_610 = 610; // User not registered in system
    const HTTP_STATUS_CODE_500 = 500; // Server Error

    /**
     * Success response method
     *
     * @param string $msg
     * @param array $data
     * @return \Illuminate\Http\JsonResponse
     */
    protected function success(string $msg = "", array $data = [])
    {
        return response()->json([
            'code' => self::HTTP_STATUS_CODE_200,
            'message' => $msg,
            'result' => $data
        ]);
    }

    protected function successResponse($data = [], $message = "Success")
    {
        return response()->json([
            'status'  => true,
            'message' => $message,
            'data'    => $data,
        ], HttpStatus::OK);
    }
    /**
     * Error response method
     *
     * @param string $msg
     * @param int $error_code
     * @param mixed $stack_trace
     * @return \Illuminate\Http\JsonResponse
     */
    protected function error(string $msg = "", int $error_code = self::HTTP_STATUS_CODE_500, $stack_trace = null)
    {
        $response = [
            'code' => $error_code,
            'message' => $msg
        ];

        if (config('app.debug') && $stack_trace !== null) {
            $response['stack_trace'] = $stack_trace;
        }

        return response()->json($response, $error_code);
    }

    protected function statusCodes()
    {
        return [
            'OK'          => 200,
            'CREATED'     => 201,
            'BAD_REQUEST' => 400,
            'UNAUTHORIZED' => 401,
            'FORBIDDEN'   => 403,
        ];
    }
}
