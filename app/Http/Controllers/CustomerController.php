<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use App\Notifications\CustomerCreateNotification;
use App\Notifications\CustomerUpdateNotification;
use App\Notifications\CustomerDeleteNotification;

class CustomerController extends Controller
{
    public function index()
    {   
        $customers = Customer::latest()->paginate(10); // ✅ this returns paginator

        return view('admin.customers.index', compact('customers'));
    }

    public function create()
    {
        return view('admin.customers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:customers,email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
        ]);

        $customer = Customer::create($request->only('name', 'email', 'phone', 'address'));

        $customer->notify(new CustomerCreateNotification($customer));

        return redirect()->route('customers')->with('success', 'Customer created successfully!');
    }

    public function edit($id)
    {
        $customer = Customer::findOrFail($id);
        return view('admin.customers.edit', compact('customer'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:customers,id',
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:customers,email,' . $request->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
        ]);

        $customer = Customer::findOrFail($request->id);
        $customer->update($request->only('name', 'email', 'phone', 'address'));

        $customer->notify(new CustomerUpdateNotification($customer));

        return redirect()->route('customers')->with('success', 'Customer updated successfully!');
    }

    public function delete($id)
    {
        $customer = Customer::findOrFail($id);
        $customer->notify(new CustomerDeleteNotification($customer));
        $customer->delete();

        return redirect()->route('customers')->with('error', 'Customer deleted successfully!');
    }
}
