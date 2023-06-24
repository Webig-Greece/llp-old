<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware('permission:create-invoice', ['only' => ['store']]);
        $this->middleware('permission:update-invoice', ['only' => ['update']]);
        $this->middleware('permission:delete-invoice', ['only' => ['destroy']]);
    }

    public function index()
    {
        $invoices = Invoice::all();
        return response()->json($invoices);
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'subscription_plan_id' => 'required|integer|exists:subscription_plans,id',
            'amount' => 'required|numeric',
            'currency' => 'required|string|max:3',
            'status' => 'required|in:pending,paid,overdue'
        ]);

        $invoice = new Invoice([
            'user_id' => $request->user_id,
            'subscription_plan_id' => $request->subscription_plan_id,
            'amount' => $request->amount,
            'currency' => $request->currency,
            'status' => $request->status
        ]);

        $invoice->save();

        return response()->json(['message' => 'Invoice created successfully', 'invoice' => $invoice], 201);
    }

    public function show($id)
    {
        $invoice = Invoice::find($id);

        if (!$invoice) {
            return response()->json(['message' => 'Invoice not found'], 404);
        }

        return response()->json($invoice);
    }

    public function update(Request $request, $id)
    {
        $invoice = Invoice::find($id);

        if (!$invoice) {
            return response()->json(['message' => 'Invoice not found'], 404);
        }

        $request->validate([
            'user_id' => 'integer|exists:users,id',
            'subscription_plan_id' => 'integer|exists:subscription_plans,id',
            'amount' => 'numeric',
            'currency' => 'string|max:3',
            'status' => 'in:pending,paid,overdue'
        ]);

        $invoice->fill($request->all());
        $invoice->save();

        return response()->json(['message' => 'Invoice updated successfully', 'invoice' => $invoice]);
    }

    public function destroy($id)
    {
        $invoice = Invoice::find($id);

        if (!$invoice) {
            return response()->json(['message' => 'Invoice not found'], 404);
        }

        $invoice->delete();

        return response()->json(['message' => 'Invoice deleted successfully']);
    }
}
