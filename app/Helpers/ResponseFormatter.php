<?php

namespace App\Helpers;

use Illuminate\Http\Resources\Json\ResourceCollection;
class ResponseFormatter
{
    protected static $response = [
        'meta' => [
            'code' => 200,
            'status' => 'Success',
            'message' => null,
        ],
        'data' => null
    ];

    public static function success($data = null, $message = null, $paginatedResource = false)
    {
        self::$response['meta']['message'] = $message;
        self::$response['data'] = $data;

        if($data instanceof ResourceCollection && $paginatedResource == true) {
                $meta['current_page'] = $data->currentPage();
                $meta['per_page'] = $data->perPage();
                $meta['total_page'] = $data->lastPage();
                $meta['total_data'] = $data->total();

                $data = collect($data->items());
                self::$response['meta']['pagination'] = $meta;
                self::$response['data'] = $data;
        }

        if ($data instanceof \Illuminate\Pagination\Paginator || $data instanceof \Illuminate\Pagination\LengthAwarePaginator) {
            $meta['current_page'] = $data->toArray()['current_page'];
            $meta['per_page'] = $data->toArray()['per_page'];
            $meta['total_page'] = $data->toArray()['last_page'];
            $meta['total_data'] = $data->toArray()['total'];

            $data = collect($data->items());
            self::$response['meta']['pagination'] = $meta;
            self::$response['data'] = $data;
        }

        return response()->json(self::$response, self::$response['meta']['code']);
    }

    public static function error($data = null, $message = null, $code = 400, $error = null)
    {
        self::$response['meta']['status'] = 'error';
        self::$response['meta']['code'] = $code;
        self::$response['meta']['message'] = $message;
        if ($error != null) {
            self::$response['meta']['error'] = $error;
        }
        self::$response['data'] = $data;

        return response()->json(self::$response, self::$response['meta']['code']);
    }

    public static function validation($data = null, $message = null, $code = 400, $error = null)
    {
        self::$response['meta']['status'] = 'error';
        self::$response['meta']['code'] = $code;
        self::$response['meta']['message'] = $message;
        if($error != null)
        {
            self::$response['meta']['error'] = $error;
        }
        self::$response['errors'] = $data;

        return response()->json(self::$response, self::$response['meta']['code']);
    }
}
