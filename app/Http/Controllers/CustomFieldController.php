<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CustomField;
use Illuminate\Http\Request;

class CustomFieldController extends Controller
{
    public function index()
    {
        $fields = CustomField::latest()->get();
        return view('custom_fields.index', compact('fields'));
    }

    public function create()
    {
        return view('custom_fields.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:text,number,date,email,textarea',
        ]);

        CustomField::create($request->only('name', 'type'));
        return redirect()->route('custom-fields.index')->with('success', 'Field created successfully.');
    }

    public function destroy(CustomField $customField)
    {
        $customField->delete();
        return back()->with('success', 'Field deleted successfully.');
    }
}
