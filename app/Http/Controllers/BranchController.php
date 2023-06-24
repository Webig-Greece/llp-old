<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    public function __construct()
    {
        $this->middleware('CheckPermission:create_branch')->only('store');
        $this->middleware('CheckPermission:update_branch')->only('update');
        $this->middleware('CheckPermission:delete_branch')->only('destroy');
    }

    public function index()
    {
        return Branch::all();
    }

    public function store(Request $request)
    {
        $branch = Branch::create($request->all());
        return response()->json($branch, 201);
    }

    public function show(Branch $branch)
    {
        return $branch;
    }

    public function update(Request $request, Branch $branch)
    {
        $branch->update($request->all());
        return response()->json($branch, 200);
    }

    public function destroy(Branch $branch)
    {
        $branch->delete();
        return response()->json(null, 204);
    }
}
