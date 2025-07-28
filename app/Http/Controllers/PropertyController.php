<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Property;
use App\Models\Region;
use Yajra\DataTables\Facades\DataTables;



class PropertyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
public function index(Request $request)
{
    if ($request->ajax()) {
        $query = Property::with('region')->withTrashed();

        // filters
        if ($request->filled('title')) {
            $query->where('title', 'like', "%{$request->title}%");
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('region_id')) {
            $query->where('region_id', $request->region_id);
        }

        if ($request->filled('price_min')) {
            $query->where('price', '>=', $request->price_min);
        }

        if ($request->filled('price_max')) {
            $query->where('price', '<=', $request->price_max);
        }

        return DataTables::of($query)
            ->addColumn('region', function ($row) {
                return $row->region ? $row->region->name : '-';
            })
            ->addColumn('action', function ($row) {
                $editBtn = '<button class="btn btn-sm btn-primary btn-edit" data-id="'.$row->id.'">Edit</button>';
                $deleteBtn = '<button class="btn btn-sm btn-danger btn-delete" data-id="'.$row->id.'">Delete</button>';
                $restoreBtn = '';

                if ($row->deleted_at) {
                    $restoreBtn = '<button class="btn btn-sm btn-success btn-restore" data-id="'.$row->id.'">Restore</button>';
                }

                return $editBtn . ' ' . $deleteBtn . ' ' . $restoreBtn;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    $regions = Region::all();
    return view('properties.index', compact('regions'));
}

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $regions = Region::all();

        return view('properties.create',compact('regions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    $validated = $request->validate([
        'title' => 'required',
        'description' => 'required',
        'type' => 'required|in:rent,sale',
        'price' => 'required|numeric',
        'location' => 'required',
        'region_id' => 'required|exists:regions,id',
        'status' => 'required|in:available,pending,sold',
        'featured_image' => 'nullable|image|max:2048',
    ]);

    if ($request->hasFile('featured_image')) {
        $validated['featured_image'] = $request->file('featured_image')->store('properties', 'public');
    }

    Property::create($validated);
    return redirect()->route('properties.index')->with('success', 'Property created successfully.');
}


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
{
    $property = Property::findOrFail($id);
    return response()->json($property);
}


    /**
     * Update the specified resource in storage.
     */
   public function update(Request $request, $id)
{
    $validated = $request->validate([
        'title' => 'required',
        'description' => 'required',
        'type' => 'required|in:rent,sale',
        'price' => 'required|numeric',
        'location' => 'required',
        'region_id' => 'required|exists:regions,id',
        'status' => 'required|in:available,pending,sold',
        'featured_image' => 'nullable|image', 
    ]);

    $property = Property::findOrFail($id);

   
    if ($request->hasFile('featured_image')) {
        $path = $request->file('featured_image')->store('properties', 'public');
        $validated['featured_image'] = $path;
    }

    $property->update($validated);

    return response()->json(['success' => true]);
}

    /**
     * Remove the specified resource from storage.
     */
   public function destroy(Property $property)
{
    $property->delete();
    return redirect()->back()->with('success', 'Property soft deleted.');
}

public function restore($id)
{
    $property = Property::withTrashed()->findOrFail($id);
    $property->restore();
    return redirect()->back()->with('success', 'Property restored.');
}

}
