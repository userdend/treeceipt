<?php

namespace App\Http\Controllers;

use App\Models\Role;
use DB;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    public function __construct()
    {
    }

    public function list(Request $request)
    {
        $perPage = $request->input('itemsPerPage', 10);
        $sortBy = $request->input('sortBy');
        $sortOrder = $request->input('sortOrder', 'asc');

        $allowedSorts = [
            'name',
            'display_name',
            'description'
        ];

        $query = Role::select([
            'id',
            'name',
            'display_name',
            'description'
        ]);

        if ($sortBy && in_array($sortBy, $allowedSorts, true)) {
            $query->orderBy($sortBy, $sortOrder === 'desc' ? 'desc' : 'asc');
        } else {
            $query
                ->orderBy('id', 'desc')
                ->orderBy('created_at', 'desc');
        }

        $roles = $query->paginate($perPage);

        return response()->json([
            'data' => $roles->items(),
            'total' => $roles->total(),
        ]);
    }
}
