<?php

namespace App\Http\Controllers;

use App\Models\CashParameter;
use Illuminate\Http\Request;

class CashParameterController extends Controller
{
    /**
     * Display all cash parameters.
     */
    public function index()
    {
        $parameters = CashParameter::orderBy('id', 'asc')->get();

        return view('cash.parameters.index', compact('parameters'));
    }


    /**
     * Show edit form for a parameter.
     */
    public function edit($id)
    {
        $parameter = CashParameter::findOrFail($id);

        return view('cash.parameters.edit', compact('parameter'));
    }


    /**
     * Update an existing parameter.
     */
    public function update(Request $request, $id)
    {
        $parameter = CashParameter::findOrFail($id);

        $validated = $request->validate([
            'parameter_name' => 'required|string|max:255',
            'value' => 'nullable|string|max:255',
            'type' => 'nullable|string|max:255',
        ]);

        $parameter->update($validated);

        return redirect()
            ->route('cash.parameters.index')
            ->with('success', 'Cash parameter updated successfully.');
    }
}