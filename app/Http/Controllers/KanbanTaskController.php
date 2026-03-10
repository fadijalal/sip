<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Task;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class KanbanTaskController extends Controller
{
    private function allowedApplicationsQuery($user): Builder
    {
        return match ($user->role) {
            'student' => Application::query()->where('student_id', $user->id),
            'company' => Application::query()->whereHas('opportunity', fn (Builder $q) => $q->where('company_user_id', $user->id)),
            'supervisor' => Application::query()->whereHas('student', fn (Builder $q) => $q->where('supervisor_code', $user->supervisor_code)),
            default => Application::query()->whereRaw('1 = 0'),
        };
    }

    private function authorizeApplicationOrFail(Request $request, int $applicationId): Application
    {
        return $this->allowedApplicationsQuery($request->user())
            ->with(['student:id,name,supervisor_code', 'opportunity:id,title,company_user_id'])
            ->findOrFail($applicationId);
    }

    private function normalizeNullable(mixed $value): mixed
    {
        if (! is_string($value)) {
            return $value;
        }

        $value = trim($value);

        return $value === '' ? null : $value;
    }

    public function applications(Request $request): JsonResponse
    {
        $apps = $this->allowedApplicationsQuery($request->user())
            ->with(['student:id,name', 'opportunity:id,title'])
            ->latest()
            ->get();

        return response()->json([
            'status' => true,
            'data' => $apps,
        ]);
    }

    public function tasks(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'application_id' => ['required', 'integer', 'exists:applications,id'],
        ]);

        $app = $this->authorizeApplicationOrFail($request, (int) $validated['application_id']);

        $tasks = Task::with([
            'creator:id,name,role',
            'comments:id,task_id,user_id,content,created_at',
            'comments.user:id,name,role',
            'attachments:id,task_id,user_id,filename,filepath,created_at',
            'attachments.user:id,name,role',
        ])
            ->where('application_id', $app->id)
            ->orderBy('order')
            ->orderBy('id')
            ->get();

        return response()->json([
            'status' => true,
            'data' => $tasks,
        ]);
    }

    public function storeTask(Request $request): JsonResponse
    {
        abort_unless(in_array($request->user()->role, ['company', 'supervisor'], true), 403);

        $validated = $request->validate([
            'application_id' => ['required', 'integer', 'exists:applications,id'],
            'title' => ['required', 'string', 'max:255'],
            'details' => ['nullable', 'string', 'max:4000'],
            'due_date' => ['nullable', 'date'],
            'label' => ['nullable', Rule::in(['red', 'green', 'blue'])],
        ]);

        $app = $this->authorizeApplicationOrFail($request, (int) $validated['application_id']);

        $task = Task::create([
            'application_id' => $app->id,
            'created_by' => $request->user()->id,
            'title' => trim($validated['title']),
            'details' => $this->normalizeNullable($validated['details'] ?? null),
            'due_date' => $this->normalizeNullable($validated['due_date'] ?? null),
            'label' => $this->normalizeNullable($validated['label'] ?? null),
            'assigned_user' => $app->student?->name,
            'status' => 'todo',
            'order' => (Task::where('application_id', $app->id)->where('status', 'todo')->max('order') ?? 0) + 1,
        ]);

        return response()->json(['status' => true, 'data' => $task], 201);
    }

    public function updateTask(Request $request, Task $task): JsonResponse
    {
        $this->authorizeApplicationOrFail($request, (int) $task->application_id);

        $role = $request->user()->role;
        $validated = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'details' => ['nullable', 'string', 'max:4000'],
            'student_solution' => ['nullable', 'string', 'max:5000'],
            'due_date' => ['nullable', 'date'],
            'label' => ['nullable', Rule::in(['red', 'green', 'blue'])],
            'company_score' => ['nullable', 'integer', 'min:0', 'max:50'],
            'supervisor_score' => ['nullable', 'integer', 'min:0', 'max:50'],
        ]);

        $updateData = [];

        if (in_array($role, ['company', 'supervisor'], true)) {
            if (array_key_exists('title', $validated)) {
                $updateData['title'] = trim((string) $validated['title']);
            }
            if (array_key_exists('details', $validated)) {
                $updateData['details'] = $this->normalizeNullable($validated['details']);
            }
            if (array_key_exists('due_date', $validated)) {
                $updateData['due_date'] = $this->normalizeNullable($validated['due_date']);
            }
            if (array_key_exists('label', $validated)) {
                $updateData['label'] = $this->normalizeNullable($validated['label']);
            }
        }

        if ($role === 'student' && array_key_exists('student_solution', $validated)) {
            $updateData['student_solution'] = $this->normalizeNullable($validated['student_solution']);
        }

        if ($role === 'company' && array_key_exists('company_score', $validated)) {
            $updateData['company_score'] = $validated['company_score'];
        }

        if ($role === 'supervisor' && array_key_exists('supervisor_score', $validated)) {
            $updateData['supervisor_score'] = $validated['supervisor_score'];
        }

        if ($updateData !== []) {
            $task->update($updateData);
        }

        return response()->json(['status' => true, 'data' => $task->fresh()]);
    }

    public function moveTask(Request $request, Task $task): JsonResponse
    {
        abort_unless($request->user()->role === 'student', 403);

        $validated = $request->validate([
            'status' => ['required', Rule::in(['todo', 'progress', 'done'])],
            'ordered_ids' => ['nullable', 'array'],
            'ordered_ids.*' => ['integer', 'exists:tasks,id'],
        ]);

        $app = $this->authorizeApplicationOrFail($request, (int) $task->application_id);
        abort_unless((int) $app->student_id === (int) $request->user()->id, 403);

        DB::transaction(function () use ($task, $validated, $app): void {
            $task->update(['status' => $validated['status']]);

            $orderedIds = $validated['ordered_ids'] ?? [];
            foreach (array_values($orderedIds) as $index => $taskId) {
                Task::where('application_id', $app->id)
                    ->whereKey((int) $taskId)
                    ->update([
                        'status' => $validated['status'],
                        'order' => $index + 1,
                    ]);
            }
        });

        return response()->json(['status' => true, 'data' => $task->fresh()]);
    }

    public function deleteTask(Request $request, Task $task): JsonResponse
    {
        abort_unless(in_array($request->user()->role, ['company', 'supervisor'], true), 403);
        $this->authorizeApplicationOrFail($request, (int) $task->application_id);

        $task->delete();

        return response()->json(['status' => true, 'message' => 'Task deleted']);
    }

    public function addComment(Request $request, Task $task): JsonResponse
    {
        $this->authorizeApplicationOrFail($request, (int) $task->application_id);

        $validated = $request->validate([
            'content' => ['required', 'string', 'max:1000'],
        ]);

        $comment = $task->comments()->create([
            'user_id' => $request->user()->id,
            'content' => trim($validated['content']),
        ]);

        return response()->json([
            'status' => true,
            'data' => $comment->load('user:id,name,role'),
        ], 201);
    }

    public function uploadAttachment(Request $request, Task $task): JsonResponse
    {
        abort_unless($request->user()->role === 'student', 403);

        $app = $this->authorizeApplicationOrFail($request, (int) $task->application_id);
        abort_unless((int) $app->student_id === (int) $request->user()->id, 403);

        $validated = $request->validate([
            'file' => ['required', 'file', 'max:10240'],
        ]);

        $file = $validated['file'];
        $path = $file->store('attachments', 'public');

        $attachment = $task->attachments()->create([
            'user_id' => $request->user()->id,
            'filename' => $file->getClientOriginalName(),
            'filepath' => $path,
        ]);

        return response()->json([
            'status' => true,
            'data' => $attachment->load('user:id,name,role'),
        ], 201);
    }

    public function submitFinalScore(Request $request, Application $application): JsonResponse
    {
        abort_unless(in_array($request->user()->role, ['company', 'supervisor'], true), 403);
        $app = $this->authorizeApplicationOrFail($request, (int) $application->id);

        $validated = $request->validate([
            'score' => ['required', 'integer', 'min:0', 'max:100'],
        ]);

        if ($request->user()->role === 'company') {
            $app->company_final_score = $validated['score'];
        } else {
            $app->supervisor_final_score = $validated['score'];
        }

        if ($app->company_final_score !== null && $app->supervisor_final_score !== null) {
            $app->final_score = (int) round(($app->company_final_score + $app->supervisor_final_score) / 2);
        }

        $app->save();

        return response()->json(['status' => true, 'data' => $app]);
    }

    public function finishTraining(Request $request, Application $application): JsonResponse
    {
        abort_unless(in_array($request->user()->role, ['company', 'supervisor'], true), 403);
        $app = $this->authorizeApplicationOrFail($request, (int) $application->id);

        $hasUngradedDoneTasks = Task::where('application_id', $app->id)
            ->where('status', 'done')
            ->where(function (Builder $q): void {
                $q->whereNull('company_score')->orWhereNull('supervisor_score');
            })->exists();

        abort_if($hasUngradedDoneTasks, 422, 'íÌÈ ÊÞííã ßá ÇáãåÇã ÇáãäÌÒÉ ÃæáÇð.');
        abort_if($app->company_final_score === null || $app->supervisor_final_score === null, 422, 'íÌÈ ÅÏÎÇá ÚáÇãÉ ÇáÔÑßÉ æÇáãÔÑÝ ÃæáÇð.');

        $app->final_score = (int) round(($app->company_final_score + $app->supervisor_final_score) / 2);
        $app->training_completed_at = now();
        $app->save();

        return response()->json(['status' => true, 'data' => $app]);
    }
}
