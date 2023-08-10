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

        return response()->json($company, 201);
    }


    public function show(Company $company)
    {
        return $company;
    }

    public function update(Request $request, Company $company)
    {
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

        return response()->json($company, 200);
    }


    public function destroy(Company $company)
    {
        $company->delete();
        return response()->json(null, 204);
    }
}
