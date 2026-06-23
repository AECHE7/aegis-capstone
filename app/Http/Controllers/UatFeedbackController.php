<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UatFeedback;
use Illuminate\Support\Facades\Auth;

class UatFeedbackController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'functional_suitability' => 'required|integer|min:1|max:5',
            'usability' => 'required|integer|min:1|max:5',
            'reliability' => 'required|integer|min:1|max:5',
            'security' => 'required|integer|min:1|max:5',
            'comments' => 'nullable|string'
        ]);

        UatFeedback::create([
            'user_id' => Auth::id(),
            'role' => Auth::user()->role ?? 'unknown',
            'functional_suitability' => $request->functional_suitability,
            'usability' => $request->usability,
            'reliability' => $request->reliability,
            'security' => $request->security,
            'comments' => $request->comments
        ]);

        return back()->with('success', 'Thank you! Your UAT feedback has been successfully recorded in the system.');
    }
}
