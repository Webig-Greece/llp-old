<?php

namespace App\Exceptions\Company;

use Exception;

class CompanyNotFoundException extends Exception
{
    public function render($request)
    {
        return response()->json(['message' => 'Company not found'], 404);
    }
}
