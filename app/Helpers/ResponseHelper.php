<?php

function apiResponse($data, $message = null, $status = 200)
{
    return response()->json([
        'data' => $data,
        'message' => $message,
    ], $status);
}
