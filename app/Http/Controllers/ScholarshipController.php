<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Scholarship;
use App\Models\AcademicTerm;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;

class ScholarshipController extends Controller
{
    /**
     * Display the comprehensive directory of available scholarship programs and their full descriptions.
     */
    public function catalog(Request $request): View
    {
        $query = Scholarship::with('fields')
            ->where('status', 'Active');

        if ($request->filled('search')) {
            $search = '%' . trim($request->input('search')) . '%';
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', $search)
                  ->orWhere('description', 'like', $search);
            });
        }

        $scholarships = $query->orderBy('name', 'asc')->get();
        $activeTerm = AcademicTerm::where('is_active', true)->first();

        return view('scholarships.catalog', compact('scholarships', 'activeTerm'));
    }
}
