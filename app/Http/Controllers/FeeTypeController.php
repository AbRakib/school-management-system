<?php

namespace App\Http\Controllers;

use App\Models\FeeType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class FeeTypeController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        FeeType::query()->create($request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('fee_types')],
            'is_recurring' => ['boolean'],
        ]));
        return back();
    }

    public function update(Request $request, FeeType $feeType): RedirectResponse
    {
        $feeType->update($request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('fee_types')->ignore($feeType)],
            'is_recurring' => ['boolean'],
        ]));
        return back();
    }

    public function destroy(FeeType $feeType): RedirectResponse
    {
        $feeType->delete();
        return back();
    }
}
