<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Company;
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
        $branches = Branch::all();
        return response()->json($branches);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'address' => 'required|string',
            'company_id' => 'required|integer|exists:companies,id'
        ]);

        $branch = new Branch([
            'name' => $request->name,
            'address' => $request->address,
            'company_id' => $request->company_id
        ]);

        $branch->save();

        return response()->json(['message' => 'Branch created successfully', 'branch' => $branch], 201);
    }

    public function show($id)
    {
        $branch = Branch::find($id);

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
            'name' => 'string',
            'address' => 'string',
            'company_id' => 'integer|exists:companies,id'
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
