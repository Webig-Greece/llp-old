<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\User;
use App\Exceptions\Branch\BranchNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        // Get the authenticated user
        $user = User::find(Auth::id());

        // Check if the user is authorized to create a branch
        if (!$user->canCreateBranch()) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 403);
        }

        $request->validate([
            'company_id' => 'required|integer|exists:companies,id',
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'phone' => 'required|string|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|max:15',
            'email' => 'required|email|max:255'
        ]);

        $branch = new Branch([
            'company_id' => $request->company_id,
            'name' => $request->name,
            'address' => $request->address,
            'phone' => $request->phone,
            'email' => $request->email
        ]);

        $branch->save();

        return apiResponse($branch, 'Branch created successfully', 201);
    }

    public function show($id)
    {
        $branch = Branch::with('company')->find($id);

        if (!$branch) {
            throw new BranchNotFoundException();
        }

        return apiResponse($branch);
    }

    public function update(Request $request, $id)
    {
        $branch = Branch::find($id);

        if (!$branch) {
            throw new BranchNotFoundException();
        }

        $request->validate([
            'company_id' => 'integer|exists:companies,id',
            'name' => 'string|max:255',
            'address' => 'string|max:255',
            'phone' => 'string|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|max:15',
            'email' => 'email|max:255'
        ]);

        $branch->fill($request->all());
        $branch->save();

        return apiResponse($branch, 'Branch updated successfully', 200);
    }

    public function destroy($id)
    {
        $branch = Branch::find($id);

        if (!$branch) {
            throw new BranchNotFoundException();
        }

        $branch->delete();

        return apiResponse(null, 'Branch deleted successfully', 204);
    }
}
