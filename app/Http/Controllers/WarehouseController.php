<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Warehouse;
use App\Models\User;
use App\Notifications\WarehouseCreateNotification;
use App\Notifications\WarehouseUpdateNotification;
use App\Notifications\WarehouseDeleteNotification;

class WarehouseController extends Controller
{
    public function index() {
    $warehouses = Warehouse::latest()->paginate(10); // Not all()
        return view('admin.warehouses.index', compact('warehouses'));
    }

    public function create() {
        return view('admin.warehouses.create');
    }

    public function store(Request $request) {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'location' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
        ]);

        $warehouse = Warehouse::create([
            'name' => $request->name,
            'location' => $request->location,
            'description' => $request->description,
        ]);

        // Notify admin
        $admin = User::where('role', 'admin')->first(); // Ensure the 'role' column exists
        if ($admin) {
            $admin->notify(new WarehouseCreateNotification($warehouse));
        }

        return redirect()->route('warehouses')->with('success', 'Warehouse created successfully!');
    }

    public function edit($id) {
        $warehouse = Warehouse::findOrFail($id);
        return view('admin.warehouses.edit', compact('warehouse'));
    }

    public function update(Request $request) {
        $request->validate([
            'id' => ['required', 'exists:warehouses,id'],
            'name' => ['required', 'string', 'max:255'],
            'location' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
        ]);

        $warehouse = Warehouse::findOrFail($request->id);
        $warehouse->update([
            'name' => $request->name,
            'location' => $request->location,
            'description' => $request->description,
        ]);

        // Notify admin
        $admin = User::where('role', 'admin')->first();
        if ($admin) {
            $admin->notify(new WarehouseUpdateNotification($warehouse));
        }

        return redirect()->route('warehouses')->with('success', 'Warehouse updated successfully!');
    }

    public function delete($id) {
        $warehouse = Warehouse::findOrFail($id);

        $admin = User::where('role', 'admin')->first();
        if ($admin) {
            $admin->notify(new WarehouseDeleteNotification($warehouse));
        }

        $warehouse->delete();

        return redirect()->route('warehouses')->with('error', 'Warehouse deleted successfully!');
    }
}
