<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EmployeeController extends Controller
{
    /**
     * GET /api/employees
     * List employees dengan pagination + filter + search
     */
    public function index(Request $request)
    {
        $query = Employee::query();

        // Filter status (active / inactive)
        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        // Search di name / email
        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        // Pagination (default 10)
        $perPage = (int) $request->query('per_page', 10);
        if ($perPage <= 0) {
            $perPage = 10;
        }

        $employees = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Employee list retrieved successfully.',
            'data'    => $employees->items(),
            'meta'    => [
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
     * POST /api/employees
     * Tambah employee baru
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'      => 'required|string|max:100',
            'email'     => 'required|email|unique:employees,email',
            'position'  => 'required|string',
            'salary'    => 'required|integer|min:2000000|max:50000000',
            'status'    => 'sometimes|in:active,inactive',
            'hired_at'  => 'sometimes|date',
        ]);

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

        $employee = Employee::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Employee created successfully.',
            'data'    => $employee,
        ], 201);
    }

    /**
     * GET /api/employees/{id}
     * Ambil detail 1 employee
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
     * PUT /api/employees/{id}
     * Update employee
     */
    public function update(Request $request, string $id)
    {
        $employee = Employee::find($id);

        if (! $employee) {
            return response()->json([
                'success' => false,
                'message' => 'Employee not found.',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name'      => 'required|string|max:100',
            'email'     => 'required|email|unique:employees,email,' . $employee->id,
            'position'  => 'required|string',
            'salary'    => 'required|integer|min:2000000|max:50000000',
            'status'    => 'sometimes|in:active,inactive',
            'hired_at'  => 'sometimes|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        $employee->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Employee updated successfully.',
            'data'    => $employee,
        ], 200);
    }

    /**
     * DELETE /api/employees/{id}
     * Soft delete employee
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
