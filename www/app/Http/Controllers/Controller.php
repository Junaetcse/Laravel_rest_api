<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Response as HttpResponse;


class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    private $http_status_code = 200;
    //Check vendor\symfony\http-foundation\Response.php for all HTTP status codes
    //e.g., HttpResponse::HTTP_OK

    protected function statusCode($code)
    {
        $this->http_status_code = $code;
        return $this;
    }

    protected function respond($data = [], $headers = [])
    {
        $data = is_array($data)? $data : compact('data');

        $response_array = array_merge([
            'status' => 'OK'
        ], $data);

        return response()->json($response_array, $this->http_status_code, $headers);
    }

    protected function respondNotFound($data = [], $headers = [])
    {
        $data = $data ?: ['name' => 'Resource Not Found'];

        return $this
            ->statusCode(HttpResponse::HTTP_NOT_FOUND)
            ->respondError($data, $headers);
    }

    protected function respondError($data = [], $headers = [])
    {
        return $this
            ->statusCode(500)
            ->respond([
                'status' => 'ERROR',
                'message' => $data
            ], $headers);
    }

    protected function respondValidationError($data = [], $headers = [])
    {
        $data = $data ?: ['name' => 'Validation Error'];

        return $this
            ->statusCode(HttpResponse::HTTP_BAD_REQUEST)
            ->respondError($data, $headers);

    }
}
