<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Jobs\SendCustomerToZoho;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::query();

        if ($request->has('search') && $request->search) {
            $query->search($request->search);
        }

        if ($request->has('type')) {
            switch ($request->type) {
                case 'customer':
                    $query->customers();
                    break;
                case 'vendor':
                    $query->vendors();
                    break;
            }
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $sortBy = $request->get('sortBy', 'contact_name');
        $sortOrder = $request->get('sortOrder', 'asc');
        $query->orderBy($sortBy, $sortOrder);

        $perPage = $request->get('perPage', 20);
        $customers = $query->paginate($perPage);

        return response()->json($customers);
    }

    public function show($id)
    {
        $customer = Customer::findOrFail($id);
        return response()->json($customer);
    }

    public function showByContactId($contactId)
    {
        $customer = Customer::where('contact_id', $contactId)->firstOrFail();
        return response()->json($customer);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'contact_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'contact_type' => 'required|in:customer,vendor,both',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $customer = Customer::create([
            'contact_name' => $request->contact_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'mobile' => $request->mobile,
            'contact_type' => $request->contact_type,
            'status' => 'wait'
        ]);

        SendCustomerToZoho::dispatch($customer);

        return response()->json([
            'success' => true,
            'data' => $customer,
            'message' => 'Клієнта створено успішно'
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'contact_name' => 'sometimes|required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'contact_type' => 'sometimes|required|in:customer,vendor,both',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $customer->update($request->all());

        return response()->json([
            'success' => true,
            'data' => $customer,
            'message' => 'Клієнта оновлено успішно'
        ]);
    }

    public function destroy($id)
    {
        $customer = Customer::findOrFail($id);
        $customer->delete();

        return response()->json([
            'success' => true,
            'message' => 'Клієнта видалено успішно'
        ]);
    }

    public function statistics()
    {
        $stats = [
            'total' => Customer::count(),
            'active' => Customer::active()->count(),
            'customers' => Customer::customers()->count(),
            'vendors' => Customer::vendors()->count(),
            'with_debt' => Customer::where('outstanding_receivable_amount', '>', 0)->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }
}

