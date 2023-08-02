<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\User;
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

    public function createBranch(Request $request)
    {
        // Validate the request data
        $request->validate([
            'name' => 'required|string',
            'address' => 'required|string',
            // Add other necessary fields
        ]);

        // Get the authenticated user
        $user = User::find(Auth::id());

        // Check if the user is authorized to create a branch
        if (!$user->canCreateBranch()) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 403);
        }

        // Create the branch
        $branch = new Branch([
            'name' => $request->input('name'),
            'address' => $request->input('address'),
            // Add other necessary fields
            'company_id' => $user->company_id, // Associate with the user's company
        ]);
        $branch->save();

        return response()->json([
            'message' => 'Successfully created branch!'
        ], 201);
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
