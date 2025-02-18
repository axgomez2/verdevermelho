<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Equipment;
use App\Models\EquipmentCategory;
use App\Models\Product;
use App\Models\ProductType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EquipmentController extends Controller
{
    public function index()
    {
        $equipments = Equipment::with(['category', 'productType'])->paginate(50);
        return view('admin.equipments.index', compact('equipments'));
    }

    public function create()
    {
        $categories = EquipmentCategory::all();
        $productTypes = ProductType::all();
        return view('admin.equipments.create', compact('categories', 'productTypes'));
    }

    private function validateEquipmentData(Request $request)
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:equipment_categories,id',
            'product_type_id' => 'required|exists:product_types,id',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
        ]);
    }




    public function store(Request $request)
    {
        $validatedData = $this->handleEquipmentData($request);

        try {
            DB::beginTransaction();
            Equipment::create($validatedData);
            DB::commit();
            return redirect()->route('admin.equipments.index')->with('success', 'Equipment created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating equipment: ' . $e->getMessage());
            return back()->withInput()->with('error', 'An error occurred while creating the equipment.');
        }
    }

    public function edit($id)
    {
        $equipment = Equipment::findOrFail($id);
        $categories = EquipmentCategory::all();
        $productTypes = ProductType::all();
        return view('admin.equipments.edit', compact('equipment', 'categories', 'productTypes'));
    }

    public function update(Request $request, $id)
    {
        $equipment = Equipment::findOrFail($id);

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:equipment_categories,id',
            'product_type_id' => 'required|exists:product_types,id',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
        ]);

        try {
            DB::beginTransaction();
            $equipment->update($validatedData);
            DB::commit();
            return redirect()->route('admin.equipments.index')->with('success', 'Equipment updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating equipment: ' . $e->getMessage());
            return back()->withInput()->with('error', 'An error occurred while updating the equipment.');
        }
    }

    public function destroy($id)
    {
        $equipment = Equipment::findOrFail($id);

        try {
            DB::beginTransaction();
            $equipment->delete();
            DB::commit();
            return redirect()->route('admin.equipments.index')->with('success', 'Equipment deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting equipment: ' . $e->getMessage());
            return redirect()->route('admin.equipments.index')->with('error', 'An error occurred while deleting the equipment.');
        }
    }
}
