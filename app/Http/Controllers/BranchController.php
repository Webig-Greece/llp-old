<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware('permission:create-branch', ['only' => ['store']]);
        $this->middleware('permission:update-branch', ['only' => ['update']]);
        $this->middleware('permission:delete-branch', ['only' => ['destroy']]);
    }

    public function index()
    {
        $branches = Branch::with('company')->get();
        return response()->json($branches);
    }

    public function store(Request $request)
    {
        $request->validate([
            'company_id' => 'required|integer|exists:companies,id',
            'name' => 'required|string',
            'address' => 'required|string',
            'phone' => 'required|string',
            'email' => 'required|email'
        ]);

        $branch = new Branch([
            'company_id' => $request->company_id,
            'name' => $request->name,
            'address' => $request->address,
            'phone' => $request->phone,
            'email' => $request->email
        ]);

        $branch->save();

        return response()->json(['message' => 'Branch created successfully', 'branch' => $branch], 201);
    }

    public function show($id)
    {
        $branch = Branch::with('company')->find($id);

        if (!$branch) {
            return response()->json(['message' => 'Branch not found'], 404);
        }

        return response()->json($branch);
    }

    public function update(Request $request, $id)
    {
        $branch = Branch::find($id);

        if (!$branch) {
            return response()->json(['message' => 'Branch not found'], 404);
        }

        $request->validate([
            'company_id' => 'integer|exists:companies,id',
            'name' => 'string',
            'address' => 'string',
            'phone' => 'string',
            'email' => 'email'
        ]);

        $branch->fill($request->all());
        $branch->save();

        return response()->json(['message' => 'Branch updated successfully', 'branch' => $branch]);
    }

    public function destroy($id)
    {
        $branch = Branch::find($id);

        if (!$branch) {
            return response()->json(['message' => 'Branch not found'], 404);
        }

        $branch->delete();

        return response()->json(['message' => 'Branch deleted successfully']);
    }
}
