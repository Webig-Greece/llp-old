<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use App\Exceptions\Company\CompanyNotFoundException;

class CompanyController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware('permission:create-company', ['only' => ['store']]);
        $this->middleware('permission:update-company', ['only' => ['update']]);
        $this->middleware('permission:delete-company', ['only' => ['destroy']]);
    }

    public function index()
    {
        return Company::all();
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'vat_number' => 'required|string|max:50|unique:companies',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|string|email|max:255',
            'billing_address' => 'nullable|string|max:255',
            'subscription_plan_id' => 'nullable|integer|exists:subscription_plans,id',
            'subscription_expiry' => 'nullable|date',
        ]);

        $company = Company::create($request->all());

        return apiResponse($company, 'Company created successfully', 201);
    }


    public function show($id)
    {
        $company = Company::find($id);

        if (!$company) {
            throw new CompanyNotFoundException();
        }

        return apiResponse($company);
    }

    public function update(Request $request, $id)
    {
        $company = Company::find($id);

        if (!$company) {
            throw new CompanyNotFoundException();
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'vat_number' => 'required|string|max:50|unique:companies,vat_number,' . $company->id,
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|string|email|max:255',
            'billing_address' => 'nullable|string|max:255',
            'subscription_plan_id' => 'nullable|integer|exists:subscription_plans,id',
            'subscription_expiry' => 'nullable|date',
        ]);

        $company->update($request->all());

        return apiResponse($company, 'Company updated successfully', 200);
    }


    public function destroy($id)
    {
        $company = Company::find($id);

        if (!$company) {
            throw new CompanyNotFoundException();
        }

        $company->delete();
        return apiResponse(null, 'Company deleted successfully', 204);
    }
}
