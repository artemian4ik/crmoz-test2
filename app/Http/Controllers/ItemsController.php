<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Items;

class ItemsController extends Controller
{
    public function index(Request $request)
    {
        $query = Items::query();
        
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('item_name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%")
                  ->orWhere('item_id', 'like', "%{$search}%");
            });
        }

        $sortBy = $request->get('sortBy', 'created_at');
        $sortOrder = $request->get('sortOrder', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $perPage = $request->get('perPage', 20);
        $items = $query->paginate($perPage);

        return response()->json($items);
    }

    public function getItem(Request $request)
    {
        $item = Items::where('item_id', $request->id)->firstOrFail();
        return response()->json($item);
    }
}

