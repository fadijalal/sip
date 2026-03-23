<?php

namespace App\Http\Controllers\company;

use App\Http\Controllers\Controller;
use App\Models\InternshipOpportunity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class JobController extends Controller
{
    public function createJob(Request $request): RedirectResponse
    {
        abort_unless($request->user() && $request->user()->role === 'company', 403);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'type' => ['required', 'in:training,job'],
            'field' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'work_type' => ['nullable', 'in:onsite,remote,hybrid'],
            'requirements' => ['nullable', 'string'],
            'education_level' => ['nullable', 'string', 'max:255'],
            'duration' => ['nullable', 'integer', 'min:1'],
            'deadline' => ['nullable', 'date'],
            'status' => ['nullable', 'in:open,closed'],
        ]);

        InternshipOpportunity::create([
            'company_user_id' => $request->user()->id,
            'title' => $validated['title'],
            'description' => $validated['description'],
            'type' => $validated['type'],
            'field' => $validated['field'] ?? null,
            'city' => $validated['city'] ?? null,
            'work_type' => $validated['work_type'] ?? null,
            'requirements' => $validated['requirements'] ?? null,
            'education_level' => $validated['education_level'] ?? null,
            'duration' => $validated['duration'] ?? null,
            'deadline' => $validated['deadline'] ?? null,
            'status' => $validated['status'] ?? 'open',
        ]);

        return redirect()->route('company.programs.index')->with('success', 'Job created successfully.');
    }

    public function updateJob(Request $request, int $id): RedirectResponse
    {
        abort_unless($request->user() && $request->user()->role === 'company', 403);

        $job = InternshipOpportunity::findOrFail($id);
        abort_unless((int) $job->company_user_id === (int) $request->user()->id, 403);

        $validated = $request->validate([
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['sometimes', 'required', 'string'],
            'type' => ['sometimes', 'required', 'in:training,job'],
            'field' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'work_type' => ['nullable', 'in:onsite,remote,hybrid'],
            'requirements' => ['nullable', 'string'],
            'education_level' => ['nullable', 'string', 'max:255'],
            'duration' => ['nullable', 'integer', 'min:1'],
            'deadline' => ['nullable', 'date'],
            'status' => ['nullable', 'in:open,closed'],
        ]);

        $job->update($validated);

        return redirect()->route('company.programs.index')->with('success', 'Job updated successfully.');
    }

    public function deleteJob(Request $request, int $id): RedirectResponse
    {
        abort_unless($request->user() && $request->user()->role === 'company', 403);

        $job = InternshipOpportunity::findOrFail($id);
        abort_unless((int) $job->company_user_id === (int) $request->user()->id, 403);

        $job->delete();

        return redirect()->route('company.programs.index')->with('success', 'Job deleted successfully.');
    }
}
