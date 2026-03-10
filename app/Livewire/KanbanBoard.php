<?php

namespace App\Livewire;

use App\Models\Application;
use App\Models\Task;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

class KanbanBoard extends Component
{
    use WithFileUploads;

    public ?int $selectedApplicationId = null;
    public array $applicationOptions = [];

    public string $title = '';
    public string $details = '';
    public string $due_date = '';
    public string $label = '';

    public array $todo = [];
    public array $progress = [];
    public array $done = [];

    public ?int $editTaskId = null;
    public string $editTitle = '';
    public string $editDetails = '';
    public string $editStudentSolution = '';
    public string $editDueDate = '';
    public string $editLabel = '';
    public ?int $editCompanyScore = null;
    public ?int $editSupervisorScore = null;
    public bool $showEditPopup = false;

    public array $existingComments = [];
    public string $newComment = '';
    public array $existingAttachments = [];
    public array $editAttachments = [];

    public ?int $finalRoleScore = null;

    protected function rules(): array
    {
        return [
            'selectedApplicationId' => ['required', 'integer', 'exists:applications,id'],
            'title' => ['required', 'string', 'max:255'],
            'details' => ['nullable', 'string', 'max:4000'],
            'due_date' => ['nullable', 'date'],
            'label' => ['nullable', Rule::in(['red', 'green', 'blue'])],
            'editTaskId' => ['required', 'integer', 'exists:tasks,id'],
            'editTitle' => ['required', 'string', 'max:255'],
            'editDetails' => ['nullable', 'string', 'max:4000'],
            'editStudentSolution' => ['nullable', 'string', 'max:5000'],
            'editDueDate' => ['nullable', 'date'],
            'editLabel' => ['nullable', Rule::in(['red', 'green', 'blue'])],
            'editCompanyScore' => ['nullable', 'integer', 'min:0', 'max:50'],
            'editSupervisorScore' => ['nullable', 'integer', 'min:0', 'max:50'],
            'newComment' => ['nullable', 'string', 'max:1000'],
            'editAttachments.*' => ['nullable', 'file', 'max:10240'],
            'finalRoleScore' => ['nullable', 'integer', 'min:0', 'max:100'],
        ];
    }

    protected array $messages = [
        'title.required' => 'ÚäæÇä ÇáãåãÉ ãØáæÈ.',
        'title.max' => 'ÚäæÇä ÇáãåãÉ Øæíá ÌÏðÇ.',
        'details.max' => 'ÊÝÇÕíá ÇáãåãÉ íÌÈ Ãä Êßæä ÃÞá ãä 4000 ÍÑÝ.',
        'label.in' => 'ÇáæÓã ÇáãÎÊÇÑ ÛíÑ ãÏÚæã.',
        'newComment.max' => 'ÇáÊÚáíÞ Øæíá ÌÏðÇ.',
        'editAttachments.*.max' => 'ßá ãáÝ íÌÈ ÃáÇ íÊÌÇæÒ 10MB.',
        'editCompanyScore.max' => 'ÚáÇãÉ ÇáÔÑßÉ áßá ãåãÉ ãä 50.',
        'editSupervisorScore.max' => 'ÚáÇãÉ ÇáãÔÑÝ áßá ãåãÉ ãä 50.',
        'finalRoleScore.max' => 'ÇáÚáÇãÉ ÇáäåÇÆíÉ áßá ÏæÑ ãä 100.',
    ];

    public function mount(): void
    {
        abort_unless(Auth::check(), 403);

        $this->loadApplications();

        if ($this->applicationOptions !== []) {
            $this->selectedApplicationId = $this->applicationOptions[0]['id'];
            $this->loadTasks();
        }
    }

    public function updatedSelectedApplicationId(): void
    {
        $this->resetTaskState();
        $this->loadTasks();
    }

    private function userRole(): string
    {
        return (string) Auth::user()->role;
    }

    public function canCreateTask(): bool
    {
        return in_array($this->userRole(), ['company', 'supervisor'], true);
    }

    public function isStudent(): bool
    {
        return $this->userRole() === 'student';
    }

    private function normalizeNullable(?string $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    private function allowedApplicationsQuery(): Builder
    {
        $user = Auth::user();

        return match ($user->role) {
            'student' => Application::query()->where('student_id', $user->id),
            'company' => Application::query()->whereHas('opportunity', fn (Builder $q) => $q->where('company_user_id', $user->id)),
            'supervisor' => Application::query()->whereHas('student', fn (Builder $q) => $q->where('supervisor_code', $user->supervisor_code)),
            default => Application::query()->whereRaw('1 = 0'),
        };
    }

    private function authorizedApplicationOrFail(int $id): Application
    {
        return $this->allowedApplicationsQuery()
            ->with(['student:id,name,supervisor_code', 'opportunity:id,title,company_user_id'])
            ->findOrFail($id);
    }

    public function loadApplications(): void
    {
        $apps = $this->allowedApplicationsQuery()
            ->with(['student:id,name', 'opportunity:id,title'])
            ->latest()
            ->get();

        $this->applicationOptions = $apps->map(fn (Application $app) => [
            'id' => $app->id,
            'student_name' => $app->student?->name ?? 'Unknown Student',
            'opportunity_title' => $app->opportunity?->title ?? 'Unknown Opportunity',
            'final_score' => $app->final_score,
            'training_completed_at' => $app->training_completed_at,
        ])->all();
    }

    public function loadTasks(): void
    {
        if (! $this->selectedApplicationId) {
            $this->todo = [];
            $this->progress = [];
            $this->done = [];

            return;
        }

        $application = $this->authorizedApplicationOrFail($this->selectedApplicationId);

        $tasks = Task::with([
            'creator:id,name,role',
            'comments:id,task_id,user_id,content,created_at',
            'comments.user:id,name,role',
            'attachments:id,task_id,user_id,filename,filepath,created_at',
            'attachments.user:id,name,role',
        ])
            ->where('application_id', $application->id)
            ->orderBy('order')
            ->orderBy('id')
            ->get();

        $this->todo = $tasks->where('status', 'todo')->values()->all();
        $this->progress = $tasks->where('status', 'progress')->values()->all();
        $this->done = $tasks->where('status', 'done')->values()->all();

        $this->dispatch('kanban-refresh-sortables');
    }

    public function addTask(): void
    {
        abort_unless($this->canCreateTask(), 403);

        $validated = $this->validateOnlyGroup(['selectedApplicationId', 'title', 'details', 'due_date', 'label']);
        $app = $this->authorizedApplicationOrFail((int) $validated['selectedApplicationId']);

        Task::create([
            'application_id' => $app->id,
            'created_by' => Auth::id(),
            'title' => trim($validated['title']),
            'details' => $this->normalizeNullable($validated['details'] ?? null),
            'due_date' => $this->normalizeNullable($validated['due_date'] ?? null),
            'label' => $this->normalizeNullable($validated['label'] ?? null),
            'assigned_user' => $app->student?->name,
            'status' => 'todo',
            'order' => (Task::where('application_id', $app->id)->where('status', 'todo')->max('order') ?? 0) + 1,
        ]);

        $this->reset(['title', 'details', 'due_date', 'label']);
        $this->loadTasks();
    }

    public function editTask(int $id): void
    {
        $task = Task::with(['application.student', 'application.opportunity', 'comments.user', 'attachments.user', 'creator'])->findOrFail($id);
        $this->authorizedApplicationOrFail((int) $task->application_id);

        $this->editTaskId = $task->id;
        $this->editTitle = (string) $task->title;
        $this->editDetails = (string) ($task->details ?? '');
        $this->editStudentSolution = (string) ($task->student_solution ?? '');
        $this->editDueDate = $task->due_date ? $task->due_date->format('Y-m-d') : '';
        $this->editLabel = (string) ($task->label ?? '');
        $this->editCompanyScore = $task->company_score;
        $this->editSupervisorScore = $task->supervisor_score;

        $this->existingComments = $task->comments->map(fn ($comment) => [
            'id' => $comment->id,
            'content' => $comment->content,
            'author' => $comment->user?->name ?? 'Unknown',
            'author_role' => $comment->user?->role ?? '-',
            'created_at' => $comment->created_at?->format('Y-m-d H:i') ?? '-',
        ])->all();

        $this->existingAttachments = $task->attachments->map(fn ($attachment) => [
            'id' => $attachment->id,
            'filename' => $attachment->filename,
            'filepath' => $attachment->filepath,
            'author' => $attachment->user?->name ?? 'Unknown',
            'author_role' => $attachment->user?->role ?? '-',
        ])->all();

        $this->editAttachments = [];
        $this->newComment = '';
        $this->showEditPopup = true;
        $this->resetValidation();
    }

    public function updateTask(): void
    {
        $validated = $this->validateOnlyGroup([
            'editTaskId',
            'editTitle',
            'editDetails',
            'editStudentSolution',
            'editDueDate',
            'editLabel',
            'editCompanyScore',
            'editSupervisorScore',
            'editAttachments.*',
        ]);

        $task = Task::findOrFail((int) $validated['editTaskId']);
        $this->authorizedApplicationOrFail((int) $task->application_id);

        DB::transaction(function () use ($task, $validated): void {
            $role = $this->userRole();
            $updateData = [];

            if (in_array($role, ['company', 'supervisor'], true)) {
                $updateData = array_merge($updateData, [
                    'title' => trim($validated['editTitle']),
                    'details' => $this->normalizeNullable($validated['editDetails'] ?? null),
                    'due_date' => $this->normalizeNullable($validated['editDueDate'] ?? null),
                    'label' => $this->normalizeNullable($validated['editLabel'] ?? null),
                ]);
            }

            if ($role === 'student') {
                $updateData['student_solution'] = $this->normalizeNullable($validated['editStudentSolution'] ?? null);
            }

            if ($role === 'company') {
                $updateData['company_score'] = $validated['editCompanyScore'];
            }

            if ($role === 'supervisor') {
                $updateData['supervisor_score'] = $validated['editSupervisorScore'];
            }

            if ($updateData !== []) {
                $task->update($updateData);
            }

            if ($role === 'student') {
                foreach ((array) $this->editAttachments as $file) {
                    if ($file instanceof TemporaryUploadedFile) {
                        $path = $file->store('attachments', 'public');
                        $task->attachments()->create([
                            'user_id' => Auth::id(),
                            'filename' => $file->getClientOriginalName(),
                            'filepath' => $path,
                        ]);
                    }
                }
            }
        });

        $this->editAttachments = [];
        $this->loadTasks();
        $this->editTask($task->id);
    }

    public function addComment(): void
    {
        $validated = $this->validateOnlyGroup(['editTaskId', 'newComment']);
        $comment = trim((string) ($validated['newComment'] ?? ''));

        if ($comment === '') {
            return;
        }

        $task = Task::findOrFail((int) $validated['editTaskId']);
        $this->authorizedApplicationOrFail((int) $task->application_id);

        $task->comments()->create([
            'user_id' => Auth::id(),
            'content' => $comment,
        ]);

        $this->newComment = '';
        $this->editTask($task->id);
        $this->loadTasks();
    }

    public function deleteTask(int $id): void
    {
        abort_unless(in_array($this->userRole(), ['company', 'supervisor'], true), 403);

        $task = Task::findOrFail($id);
        $this->authorizedApplicationOrFail((int) $task->application_id);

        $task->delete();
        $this->closeEditPopup();
        $this->loadTasks();
    }

    public function moveTask(int $id, string $status, array $orderedIds = []): void
    {
        abort_unless($this->isStudent(), 403);

        if (! in_array($status, ['todo', 'progress', 'done'], true)) {
            return;
        }

        DB::transaction(function () use ($id, $status, $orderedIds): void {
            $task = Task::findOrFail($id);
            $app = $this->authorizedApplicationOrFail((int) $task->application_id);

            abort_unless((int) $app->student_id === (int) Auth::id(), 403);

            $task->update(['status' => $status]);

            if ($orderedIds !== []) {
                foreach (array_values($orderedIds) as $index => $taskId) {
                    Task::where('application_id', $app->id)
                        ->whereKey((int) $taskId)
                        ->update([
                            'status' => $status,
                            'order' => $index + 1,
                        ]);
                }
            }

            $this->normalizeColumnOrder($app->id, 'todo');
            $this->normalizeColumnOrder($app->id, 'progress');
            $this->normalizeColumnOrder($app->id, 'done');
        });

        $this->loadTasks();
    }

    private function normalizeColumnOrder(int $applicationId, string $status): void
    {
        $ids = Task::where('application_id', $applicationId)
            ->where('status', $status)
            ->orderBy('order')
            ->orderBy('id')
            ->pluck('id')
            ->all();

        foreach ($ids as $index => $taskId) {
            Task::whereKey($taskId)->update(['order' => $index + 1]);
        }
    }

    public function submitFinalRoleScore(): void
    {
        abort_unless(in_array($this->userRole(), ['company', 'supervisor'], true), 403);

        $validated = $this->validateOnlyGroup(['selectedApplicationId', 'finalRoleScore']);
        $app = $this->authorizedApplicationOrFail((int) $validated['selectedApplicationId']);

        if ($this->userRole() === 'company') {
            $app->company_final_score = $validated['finalRoleScore'];
        } else {
            $app->supervisor_final_score = $validated['finalRoleScore'];
        }

        if ($app->company_final_score !== null && $app->supervisor_final_score !== null) {
            $app->final_score = (int) round(($app->company_final_score + $app->supervisor_final_score) / 2);
        }

        $app->save();
        $this->loadApplications();
    }

    public function finishTraining(): void
    {
        abort_unless(in_array($this->userRole(), ['company', 'supervisor'], true), 403);

        $app = $this->authorizedApplicationOrFail((int) $this->selectedApplicationId);

        $hasUngradedDoneTasks = Task::where('application_id', $app->id)
            ->where('status', 'done')
            ->where(function (Builder $q) {
                $q->whereNull('company_score')->orWhereNull('supervisor_score');
            })->exists();

        abort_if($hasUngradedDoneTasks, 422, 'íÌÈ ÊÞííã ßá ÇáãåÇã ÇáãäÌÒÉ ÃæáÇð.');
        abort_if($app->company_final_score === null || $app->supervisor_final_score === null, 422, 'íÌÈ ÅÏÎÇá ÚáÇãÉ ÇáÔÑßÉ æÇáãÔÑÝ ÃæáÇð.');

        $app->final_score = (int) round(($app->company_final_score + $app->supervisor_final_score) / 2);
        $app->training_completed_at = now();
        $app->save();

        $this->redirectRoute('training.complete', ['application' => $app->id], navigate: true);
    }

    public function closeEditPopup(): void
    {
        $this->showEditPopup = false;
        $this->editAttachments = [];
        $this->newComment = '';
        $this->resetValidation();
    }

    private function resetTaskState(): void
    {
        $this->todo = [];
        $this->progress = [];
        $this->done = [];
        $this->closeEditPopup();
    }

    private function validateOnlyGroup(array $keys): array
    {
        $rules = collect($this->rules())->only($keys)->all();

        return $this->validate($rules, $this->messages);
    }

    public function getCurrentApplicationProperty(): ?Application
    {
        if (! $this->selectedApplicationId) {
            return null;
        }

        return $this->allowedApplicationsQuery()->find($this->selectedApplicationId);
    }

    public function render()
    {
        return view('livewire.kanban-board', [
            'role' => $this->userRole(),
            'currentApplication' => $this->currentApplication,
        ]);
    }
}
