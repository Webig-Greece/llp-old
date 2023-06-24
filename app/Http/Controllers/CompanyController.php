<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function __construct()
    {
        $this->middleware('CheckPermission:create_company')->only('store');
        $this->middleware('CheckPermission:update_company')->only('update');
        $this->middleware('CheckPermission:delete_company')->only('destroy');
    }

    public function index()
    {
        return Company::all();
    }

    public function store(Request $request)
    {
        $company = Company::create($request->all());
        return response()->json($company, 201);
    }

    public function show(Company $company)
    {
        return $company;
    }

    public function update(Request $request, Company $company)
    {
        $company->update($request->all());
        return response()->json($company, 200);
    }

    public function destroy(Company $company)
    {
        $company->delete();
        return response()->json(null, 204);
    }
}
