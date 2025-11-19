<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Models\Employee;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // base query
        $query = Employee::query();

        // filter by status (active / inactive) jika ada ?status=
        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        // filter search di name / email jika ada ?search=
        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        // pagination: default 10 per page, bisa override pakai ?per_page=xx
        $perPage = (int) $request->query('per_page', 10);
        if ($perPage <= 0) {
            $perPage = 10;
        }

        $employees = $query->paginate($perPage);

        // response JSON rapi
        return response()->json([
            'success' => true,
            'message' => 'Employee list retrieved successfully.',
            'data' => $employees->items(),
            'meta' => [
                'current_page' => $employees->currentPage(),
                'per_page'     => $employees->perPage(),
                'total'        => $employees->total(),
                'last_page'    => $employees->lastPage(),
                'from'         => $employees->firstItem(),
                'to'           => $employees->lastItem(),
            ],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'name'      => 'required|string|max:100',
            'email'     => 'required|email|unique:employees,email',
            'position'  => 'required|string',
            'salary'    => 'required|integer|min:2000000|max:50000000',
            'status'    => 'sometimes|in:active,inactive', // optional
            'hired_at'  => 'sometimes|date',               // optional
        ]);

        // Jika validasi gagal → 422
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        // Default status = active jika tidak dikirim
        if (! isset($data['status'])) {
            $data['status'] = 'active';
        }

        // Buat employee baru
        $employee = Employee::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Employee created successfully.',
            'data'    => $employee,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $employee = Employee::find($id);

        if (! $employee) {
            return response()->json([
                'success' => false,
                'message' => 'Employee not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Employee retrieved successfully.',
            'data'    => $employee,
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Cari employee
        $employee = Employee::find($id);

        if (! $employee) {
            return response()->json([
                'success' => false,
                'message' => 'Employee not found.',
            ], 404);
        }

        // Validasi input
        $validator = Validator::make($request->all(), [
            'name'      => 'required|string|max:100',
            'email'     => 'required|email|unique:employees,email,' . $employee->id,
            'position'  => 'required|string',
            'salary'    => 'required|integer|min:2000000|max:50000000',
            'status'    => 'sometimes|in:active,inactive', // optional
            'hired_at'  => 'sometimes|date',               // optional
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        // Jangan paksa default status di update:
        // kalau tidak dikirim, biarkan nilai lama tetap

        $employee->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Employee updated successfully.',
            'data'    => $employee,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $employee = Employee::find($id);

        if (! $employee) {
            return response()->json([
                'success' => false,
                'message' => 'Employee not found.',
            ], 404);
        }

        $employee->delete(); // soft delete

        return response()->json([
            'success' => true,
            'message' => 'Employee deleted successfully.',
        ], 200);
    }
}
