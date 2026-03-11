<?php

namespace App\Http\Controllers\company;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\InternshipOpportunity;
use Illuminate\Support\Facades\Auth;

class ProgramsCompanyController extends Controller
{
    public function programsPage()
    {
        abort_unless(Auth::check() && Auth::user()->role === 'company', 403);

        $companyId = Auth::id();

        $programs = InternshipOpportunity::withCount('applications')
            ->where('company_user_id', $companyId)
            ->latest()
            ->get();

        $totalPrograms = InternshipOpportunity::where('company_user_id', $companyId)->count();
        $openPrograms = InternshipOpportunity::where('company_user_id', $companyId)->where('status', 'open')->count();
        $closedPrograms = InternshipOpportunity::where('company_user_id', $companyId)->where('status', 'closed')->count();

        return view('company.programs.index', compact(
            'programs',
            'totalPrograms',
            'openPrograms',
            'closedPrograms'
        ));
    }

    public function createProgramPage()
    {
        abort_unless(Auth::check() && Auth::user()->role === 'company', 403);

        return view('company.programs.create');
    }

    public function editProgramPage($id)
    {
        abort_unless(Auth::check() && Auth::user()->role === 'company', 403);

        $program = InternshipOpportunity::where('company_user_id', Auth::id())->findOrFail($id);

        return view('company.programs.edit', compact('program'));
    }
}
