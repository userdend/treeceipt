<?php

namespace App\Http\Controllers;

use App\Models\Workspace;
use DB;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class WorkspaceController extends Controller
{
    public function __construct()
    {
    }

    public function data($id)
    {
        return Workspace::whereHas('users', function ($query) {
            $query->where('users.id', auth()->id());
        })
            ->where('id', $id)
            ->firstOrFail();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => ['required', Rule::in(['personal', 'business'])],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'taxNo' => ['nullable', 'string', 'max:255'],
            'registrationNo' => ['nullable', 'string', 'max:255'],
        ]);

        DB::beginTransaction();
        try {
            $workspace = Workspace::create([
                'type' => $validated['type'],
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'tax_no' => $validated['taxNo'] ?? null,
                'registration_no' => $validated['registrationNo'] ?? null,
            ]);

            $workspace->users()->attach(auth()->id());

            DB::commit();

            return response()->json([
                'message' => 'Workspace created successfully',
                'data' => $workspace,
            ], 201);
        } catch (Exception $e) {
            report($e);
            DB::rollBack();

            return response()->json([
                'message' => 'Failed to create workspace',
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $workspace = Workspace::whereHas('users', function ($query) {
            $query->where('users.id', auth()->id());
        })
            ->where('id', $id)
            ->firstOrFail();

        $validated = $request->validate([
            'type' => ['required', Rule::in(['personal', 'business'])],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'taxNo' => ['nullable', 'string', 'max:255'],
            'registrationNo' => ['nullable', 'string', 'max:255'],
        ]);

        DB::beginTransaction();
        try {
            $workspace->update([
                'type' => $validated['type'],
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'tax_no' => $validated['taxNo'] ?? null,
                'registration_no' => $validated['registrationNo'] ?? null,
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Successfully update workspace',
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            report($e);
            return response()->json([
                'message' => 'Failed to update workspace',
            ], 500);
        }
    }

    public function list(Request $request)
    {
        $perPage = $request->input('itemsPerPage', 10);
        $sortBy = $request->input('sortBy');
        $sortOrder = $request->input('sortOrder', 'asc');

        $allowedSorts = ['type', 'name', 'description', 'tax_no', 'registration_no'];

        $query = Workspace::select(['id', 'type', 'name', 'description', 'tax_no', 'registration_no'])
            ->whereHas('users', function ($query) {
                $query->where('users.id', auth()->id());
            });

        if ($sortBy && in_array($sortBy, $allowedSorts, true)) {
            $query->orderBy($sortBy, $sortOrder === 'desc' ? 'desc' : 'asc');
        } else {
            $query
                ->orderBy('id', 'desc')
                ->orderBy('created_at', 'desc');
        }

        $workspaces = $query->paginate($perPage);

        return response()->json([
            'data' => $workspaces->items(),
            'total' => $workspaces->total(),
        ]);
    }

    public function listMenu()
    {
        return auth()->user()
            ->workspaces()
            ->select([
                'workspaces.id',
                'workspaces.name',
                'workspaces.description',
                'workspaces.type',
                'workspaces.tax_no',
                'workspaces.registration_no'
            ])
            ->get();
    }

    public function bin(Request $request)
    {
        $perPage = $request->input('itemsPerPage', 10);
        $sortBy = $request->input('sortBy');
        $sortOrder = $request->input('sortOrder', 'asc');

        $allowedSorts = ['type', 'name', 'description', 'tax_no', 'registration_no'];

        $query = Workspace::select(['id', 'type', 'name', 'description', 'tax_no', 'registration_no'])
            ->whereHas('users', function ($query) {
                $query->where('users.id', auth()->id());
            })
            ->onlyTrashed();

        if ($sortBy && in_array($sortBy, $allowedSorts, true)) {
            $query->orderBy($sortBy, $sortOrder === 'desc' ? 'desc' : 'asc');
        } else {
            $query
                ->orderBy('id', 'desc')
                ->orderBy('created_at', 'desc');
        }

        $workspaces = $query->paginate($perPage);

        return response()->json([
            'data' => $workspaces->items(),
            'total' => $workspaces->total(),
        ]);
    }

    public function delete($id)
    {
        $workspace = Workspace::whereHas('users', function ($query) {
            $query->where('users.id', auth()->id());
        })
            ->where('id', $id)
            ->firstOrFail();

        DB::beginTransaction();
        try {
            $workspace->delete();

            DB::commit();

            return response()->json([
                'message' => 'Successfully delete workspace',
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            report($e);
            return response()->json([
                'message' => 'Failed to delete workspace',
            ], 500);
        }
    }

    public function forceDelete($id)
    {
        $workspace = Workspace::whereHas('users', function ($query) {
            $query->where('users.id', auth()->id());
        })
            ->where('id', $id)
            ->onlyTrashed()
            ->firstOrFail();

        DB::beginTransaction();
        try {
            $workspace->forceDelete();

            DB::commit();

            return response()->json([
                'message' => 'Successfully delete workspace',
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            report($e);
            return response()->json([
                'message' => 'Failed to delete workspace',
            ], 500);
        }
    }

    public function restore($id)
    {
        $workspace = Workspace::whereHas('users', function ($query) {
            $query->where('users.id', auth()->id());
        })
            ->where('id', $id)
            ->onlyTrashed()
            ->firstOrFail();

        DB::beginTransaction();
        try {
            $workspace->restore();

            DB::commit();

            return response()->json([
                'message' => 'Successfully restored workspace',
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            report($e);
            return response()->json([
                'message' => 'Failed to restored workspace',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
