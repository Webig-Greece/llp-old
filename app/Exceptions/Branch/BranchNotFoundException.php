<?php

namespace App\Exceptions\Branch;

use Exception;

class BranchNotFoundException extends Exception
{
    public function render($request)
    {
        return response()->json(['message' => 'Branch not found'], 404);
    }
}
