<div class="min-h-screen bg-[#f6f8fc] p-3 md:p-6" wire:loading.class="opacity-90 transition-opacity duration-200">
    <style>
        [x-cloak] {
            display: none !important;
        }

        .kanban-shell {
            max-width: 1500px;
            margin: 0 auto;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(10px);
            border: 1px solid #e8eef7;
            box-shadow: 0 12px 30px rgba(15, 23, 42, 0.06);
        }

        .board-head-gradient {
            background: linear-gradient(135deg, #7c3aed 0%, #6366f1 45%, #3b82f6 100%);
        }

        .kanban-column {
            min-height: 520px;
            background: #eef3f9;
            border: 1px solid #dde7f2;
            transition: all .2s ease;
        }

        .kanban-column-header {
            position: sticky;
            top: 0;
            z-index: 2;
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(8px);
        }

        .task-card {
            transition: transform 180ms ease, box-shadow 180ms ease, opacity 180ms ease;
            animation: cardIn 240ms ease-out;
            border: 1px solid #e7edf5;
        }

        .task-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 16px 28px rgba(15, 23, 42, 0.08);
        }

        .drag-handle {
            cursor: grab;
            user-select: none;
        }

        .drag-handle:active {
            cursor: grabbing;
        }

        .sortable-chosen {
            opacity: 0.86;
        }

        .sortable-drag {
            transform: rotate(1deg) scale(1.01);
            box-shadow: 0 14px 28px rgba(30, 41, 59, 0.24);
        }

        .kanban-ghost {
            background: #e0f2fe !important;
            border: 1px dashed #38bdf8 !important;
            opacity: 0.65;
        }

        .column-drop-active {
            outline: 2px dashed #60a5fa;
            outline-offset: -6px;
            background: #eff6ff !important;
        }

        .tiny-badge {
            font-size: 11px;
            font-weight: 700;
            border-radius: 999px;
            padding: 4px 10px;
            white-space: nowrap;
        }

        .label-red {
            background: #fee2e2;
            color: #dc2626;
        }

        .label-green {
            background: #dcfce7;
            color: #16a34a;
        }

        .label-blue {
            background: #dbeafe;
            color: #2563eb;
        }

        .score-pill {
            border-radius: 999px;
            padding: 5px 10px;
            font-size: 11px;
            font-weight: 700;
            background: #f8fafc;
            color: #475569;
            border: 1px solid #e2e8f0;
        }

        .status-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
        }

        .status-todo {
            background: #fff7ed;
            color: #ea580c;
        }

        .status-progress {
            background: #eff6ff;
            color: #2563eb;
        }

        .status-done {
            background: #ecfdf5;
            color: #16a34a;
        }

        .soft-input {
            width: 100%;
            border: 1px solid #d9e3ef;
            border-radius: 14px;
            background: #ffffff;
            padding: 11px 14px;
            font-size: 14px;
            color: #334155;
            transition: .2s ease;
        }

        .soft-input:focus {
            outline: none;
            border-color: #8b5cf6;
            box-shadow: 0 0 0 4px rgba(139, 92, 246, .12);
        }

        .soft-textarea {
            min-height: 110px;
            resize: vertical;
        }

        .primary-btn {
            border: none;
            border-radius: 14px;
            background: linear-gradient(135deg, #7c3aed 0%, #6366f1 100%);
            color: white;
            padding: 11px 18px;
            font-weight: 700;
            transition: .2s ease;
        }

        .primary-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 14px 25px rgba(99, 102, 241, 0.22);
        }

        .ghost-btn {
            border: 1px solid #dbe4f0;
            border-radius: 14px;
            background: #fff;
            color: #334155;
            padding: 10px 16px;
            font-weight: 700;
            transition: .2s ease;
        }

        .ghost-btn:hover {
            background: #f8fafc;
        }

        .danger-btn {
            border: none;
            border-radius: 14px;
            background: #ef4444;
            color: white;
            padding: 11px 16px;
            font-weight: 700;
        }

        .mini-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 34px;
            height: 34px;
            border-radius: 10px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            color: #64748b;
            transition: .2s ease;
        }

        .mini-action:hover {
            background: #eef2ff;
            color: #4f46e5;
        }

        .mini-action-danger:hover {
            background: #fee2e2;
            color: #dc2626;
        }

        .comment-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
        }

        .attach-row {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
        }

        @keyframes cardIn {
            from {
                opacity: 0;
                transform: translateY(6px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>

    <div class="kanban-shell space-y-6">

        {{-- HEADER --}}
        <div class="rounded-[26px] board-head-gradient p-5 md:p-6 text-white shadow-xl">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold tracking-tight">Training Workspace Board</h1>
                    <p class="mt-2 text-sm md:text-base text-white/85">
                        @if($role === 'student')
                        Move your tasks between columns, upload files, write your solution, and follow your training progress.
                        @elseif($role === 'company')
                        Create tasks for the accepted trainee, review progress, score submissions, and complete training.
                        @else
                        Follow your student's internship progress, review tasks, score work, and complete supervision.
                        @endif
                    </p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 min-w-[280px]">
                    <div class="rounded-2xl bg-white/15 p-4 border border-white/20">
                        <div class="text-xs uppercase tracking-wide text-white/75">Current Role</div>
                        <div class="mt-1 text-lg font-bold uppercase">{{ $role }}</div>
                    </div>

                    <div class="rounded-2xl bg-white/15 p-4 border border-white/20">
                        <div class="text-xs uppercase tracking-wide text-white/75">Selected Application</div>
                        <div class="mt-1 text-lg font-bold">
                            {{ $selectedApplicationId ?: '-' }}
                        </div>
                    </div>

                    <div class="rounded-2xl bg-white/15 p-4 border border-white/20">
                        <div class="text-xs uppercase tracking-wide text-white/75">Training Status</div>
                        <div class="mt-1 text-lg font-bold">
                            @if($currentApplication && $currentApplication->training_completed_at)
                            Completed
                            @else
                            Active
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- APPLICATION SELECTOR + INFO --}}
        <div class="glass-card rounded-[24px] p-4 md:p-5">
            <div class="grid grid-cols-1 xl:grid-cols-3 gap-4">
                <div class="xl:col-span-2">
                    <label class="mb-2 block text-sm font-semibold text-slate-700">
                        Application / Internship Selection
                    </label>
                    <select wire:model.live="selectedApplicationId" class="soft-input">
                        @forelse($applicationOptions as $opt)
                        <option value="{{ $opt['id'] }}">
                            #{{ $opt['id'] }} - {{ $opt['student_name'] }} | {{ $opt['opportunity_title'] }}
                        </option>
                        @empty
                        <option value="">No approved applications available</option>
                        @endforelse
                    </select>
                </div>

                <div>
                    @if($currentApplication && $currentApplication->training_completed_at)
                    <div class="h-full rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-800">
                        <div class="text-sm font-semibold">Training Completed</div>
                        <div class="mt-2 text-sm">
                            Final Score:
                            <strong>{{ $currentApplication->final_score ?? '-' }}/100</strong>
                        </div>
                    </div>
                    @elseif($currentApplication)
                    <div class="h-full rounded-2xl border border-violet-200 bg-violet-50 p-4 text-violet-800">
                        <div class="text-sm font-semibold">Current Internship</div>
                        <div class="mt-2 text-sm">
                            Company Score:
                            <strong>{{ $currentApplication->company_final_score ?? '-' }}</strong>
                        </div>
                        <div class="mt-1 text-sm">
                            Supervisor Score:
                            <strong>{{ $currentApplication->supervisor_final_score ?? '-' }}</strong>
                        </div>
                    </div>
                    @else
                    <div class="h-full rounded-2xl border border-slate-200 bg-slate-50 p-4 text-slate-600">
                        <div class="text-sm font-semibold">Board Info</div>
                        <div class="mt-2 text-sm">
                            Kanban board appears after final approval and selecting a valid application.
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- CREATE TASK SECTION --}}
        @if($this->canCreateTask() && $selectedApplicationId)
        <div class="glass-card rounded-[24px] p-4 md:p-5">
            <div class="mb-4 flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                <div>
                    <h2 class="text-lg font-bold text-slate-800">Create New Task</h2>
                    <p class="text-sm text-slate-500">
                        Add a task for this internship. The student will be able to move it across the board.
                    </p>
                </div>
                <div class="status-chip status-progress">
                    <i class="bi bi-plus-circle"></i>
                    {{ ucfirst($role) }} can create tasks
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div>
                    <input wire:model.defer="title" placeholder="Task title" class="soft-input">
                    @error('title')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <input wire:model.defer="due_date" type="date" class="soft-input">
                    @error('due_date')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <textarea wire:model.defer="details" rows="4" placeholder="Task details, deliverables, or notes..." class="soft-input soft-textarea"></textarea>
                    @error('details')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <select wire:model.defer="label" class="soft-input">
                        <option value="">Select label</option>
                        <option value="red">Urgent</option>
                        <option value="green">Low</option>
                        <option value="blue">Feature</option>
                    </select>
                </div>
            </div>

            <div class="mt-4">
                <button type="button" wire:click="addTask" class="primary-btn">
                    <i class="bi bi-plus-lg mr-1"></i>
                    Add Task
                </button>
            </div>
        </div>
        @endif

        {{-- BOARD --}}
        @if($selectedApplicationId)
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-5">
            @foreach(['todo' => 'To Do', 'progress' => 'In Progress', 'done' => 'Done'] as $key => $columnTitle)
            @php
            $statusClass = $key === 'todo' ? 'status-todo' : ($key === 'progress' ? 'status-progress' : 'status-done');
            @endphp

            <div class="rounded-[24px] bg-white border border-slate-200 shadow-sm overflow-hidden">
                <div class="kanban-column-header px-4 py-4 border-b border-slate-200 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="status-chip {{ $statusClass }}">
                            @if($key === 'todo')
                            <i class="bi bi-list-task"></i>
                            @elseif($key === 'progress')
                            <i class="bi bi-arrow-repeat"></i>
                            @else
                            <i class="bi bi-check2-circle"></i>
                            @endif
                            {{ $columnTitle }}
                        </span>
                    </div>

                    <span class="text-xs font-bold text-slate-500 bg-slate-100 rounded-full px-3 py-1">
                        {{ count($$key) }} Task(s)
                    </span>
                </div>

                <div
                    id="{{ $key }}Column"
                    data-status="{{ $key }}"
                    class="kanban-column p-3 space-y-3">
                    @forelse($$key as $task)
                    @php
                    $labelClass = match($task->label) {
                    'red' => 'label-red',
                    'green' => 'label-green',
                    'blue' => 'label-blue',
                    default => 'bg-slate-100 text-slate-600'
                    };
                    @endphp

                    <article class="task-card rounded-[18px] bg-white p-4 shadow-sm" data-id="{{ $task->id }}" wire:key="task-{{ $task->id }}">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start gap-2 mb-2">
                                    @if($role === 'student')
                                    <span class="drag-handle mt-[2px] text-slate-400 font-bold">⋮⋮</span>
                                    @endif

                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <h3 class="text-sm md:text-[15px] font-bold text-slate-800 break-words">
                                                {{ $task->title }}
                                            </h3>

                                            @if($task->label)
                                            <span class="tiny-badge {{ $labelClass }}">
                                                {{ ucfirst($task->label) }}
                                            </span>
                                            @endif
                                        </div>

                                        <p class="mt-1 text-xs text-slate-500">
                                            Created by:
                                            <span class="font-semibold">{{ $task->creator->name ?? '-' }}</span>
                                            ({{ $task->creator->role ?? '-' }})
                                        </p>
                                    </div>
                                </div>

                                @if($task->details)
                                <p class="text-sm text-slate-600 leading-6 mb-3">
                                    {{ $task->details }}
                                </p>
                                @endif

                                @if($task->student_solution)
                                <div class="mb-3 rounded-2xl border border-emerald-200 bg-emerald-50 p-3">
                                    <div class="text-xs font-bold text-emerald-700 mb-1">Student Solution</div>
                                    <p class="text-xs leading-6 text-emerald-800">
                                        {{ $task->student_solution }}
                                    </p>
                                </div>
                                @endif

                                <div class="flex flex-wrap gap-2 mb-3">
                                    <span class="score-pill">
                                        <i class="bi bi-calendar-event mr-1"></i>
                                        Due: {{ $task->due_date ? \Illuminate\Support\Carbon::parse($task->due_date)->format('Y-m-d') : '-' }}
                                    </span>

                                    <span class="score-pill">
                                        Company: {{ $task->company_score ?? 0 }}/50
                                    </span>

                                    <span class="score-pill">
                                        Supervisor: {{ $task->supervisor_score ?? 0 }}/50
                                    </span>
                                </div>
                            </div>

                            <div class="shrink-0 flex items-center gap-2">
                                <button type="button" wire:click="editTask({{ $task->id }})" class="mini-action" title="Open">
                                    <i class="bi bi-eye"></i>
                                </button>

                                @if(in_array($role, ['company', 'supervisor'], true))
                                <button
                                    type="button"
                                    onclick="if(confirm('Delete this task?')) { $wire.deleteTask({{ $task->id }}) }"
                                    class="mini-action mini-action-danger"
                                    title="Delete">
                                    <i class="bi bi-trash"></i>
                                </button>
                                @endif
                            </div>
                        </div>
                    </article>
                    @empty
                    <div class="rounded-[18px] border border-dashed border-slate-300 bg-white/70 p-6 text-center">
                        <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 text-slate-400">
                            <i class="bi bi-inbox"></i>
                        </div>
                        <h4 class="text-sm font-bold text-slate-600">No tasks here</h4>
                        <p class="mt-1 text-xs text-slate-400">
                            @if($key === 'todo')
                            New tasks will appear here first.
                            @elseif($key === 'progress')
                            Tasks being worked on appear here.
                            @else
                            Finished tasks appear here.
                            @endif
                        </p>
                    </div>
                    @endforelse
                </div>
            </div>
            @endforeach
        </div>

        {{-- FINAL SCORE SECTION --}}
        @if(in_array($role, ['company', 'supervisor'], true))
        <div class="glass-card rounded-[24px] p-4 md:p-5">
            <div class="mb-4">
                <h3 class="text-lg font-bold text-slate-800">Final Role Evaluation</h3>
                <p class="text-sm text-slate-500">
                    Submit your final evaluation score, then complete the training after both roles finish evaluation.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <input
                    type="number"
                    min="0"
                    max="100"
                    wire:model.defer="finalRoleScore"
                    class="soft-input"
                    placeholder="Enter final score (0-100)">

                <button type="button" wire:click="submitFinalRoleScore" class="ghost-btn">
                    Submit {{ ucfirst($role) }} Score
                </button>

                <button type="button" wire:click="finishTraining" class="primary-btn">
                    Mark Training Complete
                </button>
            </div>

            @error('finalRoleScore')
            <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
            @enderror

            @if($currentApplication)
            <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-3">
                <div class="rounded-2xl bg-slate-50 border border-slate-200 p-4">
                    <div class="text-xs text-slate-500">Company Final Score</div>
                    <div class="mt-1 text-lg font-bold text-slate-800">{{ $currentApplication->company_final_score ?? '-' }}</div>
                </div>

                <div class="rounded-2xl bg-slate-50 border border-slate-200 p-4">
                    <div class="text-xs text-slate-500">Supervisor Final Score</div>
                    <div class="mt-1 text-lg font-bold text-slate-800">{{ $currentApplication->supervisor_final_score ?? '-' }}</div>
                </div>

                <div class="rounded-2xl bg-violet-50 border border-violet-200 p-4">
                    <div class="text-xs text-violet-600">Combined Final Score</div>
                    <div class="mt-1 text-lg font-bold text-violet-700">{{ $currentApplication->final_score ?? '-' }}/100</div>
                </div>
            </div>
            @endif
        </div>
        @endif
        @endif
    </div>

    {{-- POPUP --}}
    <div
        x-data="{ open: $wire.entangle('showEditPopup') }"
        x-show="open"
        x-cloak
        x-transition.opacity.duration.180ms
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-3">
        <div
            x-show="open"
            x-transition:enter="transition ease-out duration-220"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-180"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="w-full max-w-4xl max-h-[92vh] overflow-y-auto rounded-[26px] bg-white p-5 md:p-6 shadow-2xl">
            <div class="mb-5 flex items-center justify-between gap-3">
                <div>
                    <h2 class="text-xl font-bold text-slate-800">Task Details</h2>
                    <p class="text-sm text-slate-500">Review, update, comment, and manage files for this task.</p>
                </div>

                <button type="button" wire:click="closeEditPopup" class="mini-action">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            @if(in_array($role, ['company', 'supervisor'], true))
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-3">
                <input wire:model.defer="editTitle" class="soft-input" placeholder="Title">
                <input wire:model.defer="editDueDate" type="date" class="soft-input">
            </div>

            <div class="mb-3">
                <textarea wire:model.defer="editDetails" rows="4" class="soft-input soft-textarea" placeholder="Details"></textarea>
            </div>

            <div class="mb-4">
                <select wire:model.defer="editLabel" class="soft-input">
                    <option value="">Label</option>
                    <option value="red">Urgent</option>
                    <option value="green">Low</option>
                    <option value="blue">Feature</option>
                </select>
            </div>
            @endif

            @if($role === 'student')
            <div class="mb-4">
                <label class="mb-2 block text-sm font-semibold text-slate-700">Your Solution</label>
                <textarea
                    wire:model.defer="editStudentSolution"
                    rows="5"
                    class="soft-input soft-textarea"
                    placeholder="Write your task solution here..."></textarea>
            </div>

            <div class="mb-4">
                <label class="mb-2 block text-sm font-semibold text-slate-700">Upload Attachments</label>
                <input type="file" wire:model="editAttachments" multiple class="soft-input">
                @error('editAttachments.*')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>
            @endif

            @if($role === 'company')
            <div class="mb-4">
                <label class="mb-2 block text-sm font-semibold text-slate-700">Company Score (0-50)</label>
                <input type="number" min="0" max="50" wire:model.defer="editCompanyScore" class="soft-input">
            </div>
            @endif

            @if($role === 'supervisor')
            <div class="mb-4">
                <label class="mb-2 block text-sm font-semibold text-slate-700">Supervisor Score (0-50)</label>
                <input type="number" min="0" max="50" wire:model.defer="editSupervisorScore" class="soft-input">
            </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                <div class="comment-box p-4">
                    <h3 class="text-sm font-bold text-slate-800 mb-3">Comments</h3>

                    <div class="space-y-2 max-h-72 overflow-y-auto pr-1">
                        @forelse($existingComments as $comment)
                        <div class="rounded-2xl bg-white border border-slate-200 p-3">
                            <div class="flex items-center justify-between gap-2">
                                <p class="text-xs font-bold text-slate-700">
                                    {{ $comment['author'] }} ({{ $comment['author_role'] }})
                                </p>
                                <p class="text-[11px] text-slate-400">{{ $comment['created_at'] }}</p>
                            </div>
                            <p class="mt-2 text-sm text-slate-600 leading-6">{{ $comment['content'] }}</p>
                        </div>
                        @empty
                        <p class="text-xs text-slate-500">No comments yet.</p>
                        @endforelse
                    </div>

                    <div class="mt-3 flex gap-2">
                        <input wire:model.defer="newComment" class="soft-input" placeholder="Write a comment...">
                        <button type="button" wire:click="addComment" class="primary-btn">Add</button>
                    </div>
                </div>

                <div class="comment-box p-4">
                    <h3 class="text-sm font-bold text-slate-800 mb-3">Attachments</h3>

                    <div class="space-y-2 max-h-72 overflow-y-auto pr-1">
                        @forelse($existingAttachments as $attachment)
                        <div class="attach-row p-3 flex items-center justify-between gap-3">
                            <div class="min-w-0">
                                <a
                                    href="{{ asset('storage/' . $attachment['filepath']) }}"
                                    target="_blank"
                                    class="block truncate text-sm font-semibold text-sky-700 underline">
                                    {{ $attachment['filename'] }}
                                </a>
                                <p class="mt-1 text-xs text-slate-500">
                                    {{ $attachment['author'] }} ({{ $attachment['author_role'] }})
                                </p>
                            </div>

                            <div class="shrink-0">
                                <a
                                    href="{{ asset('storage/' . $attachment['filepath']) }}"
                                    target="_blank"
                                    class="mini-action">
                                    <i class="bi bi-box-arrow-up-right"></i>
                                </a>
                            </div>
                        </div>
                        @empty
                        <p class="text-xs text-slate-500">No attachments yet.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="mt-5 flex flex-wrap justify-end gap-3">
                <button type="button" wire:click="closeEditPopup" class="ghost-btn">Close</button>
                <button type="button" wire:click="updateTask" class="primary-btn">Save Changes</button>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.6/Sortable.min.js"></script>
    <script>
        (() => {
            if (window.__kanbanDndBooted) return;
            window.__kanbanDndBooted = true;

            const columns = ['todoColumn', 'progressColumn', 'doneColumn'];
            const sortables = new Map();

            const initSortable = (el) => {
                if (!el || sortables.has(el.id)) return;

                sortables.set(el.id, new Sortable(el, {
                    group: 'kanban',
                    handle: '.drag-handle',
                    draggable: '.task-card',
                    animation: 220,
                    easing: 'cubic-bezier(0.2, 0.8, 0.2, 1)',
                    ghostClass: 'kanban-ghost',
                    chosenClass: 'sortable-chosen',
                    dragClass: 'sortable-drag',
                    swapThreshold: 0.65,
                    delayOnTouchOnly: true,
                    delay: 90,

                    onStart(evt) {
                        evt.from?.classList.add('column-drop-active');
                    },

                    onMove(evt) {
                        document.querySelectorAll('[data-status]').forEach((col) => col.classList.remove('column-drop-active'));
                        evt.to?.classList.add('column-drop-active');
                        return true;
                    },

                    onEnd(evt) {
                        document.querySelectorAll('[data-status]').forEach((col) => col.classList.remove('column-drop-active'));

                        const taskId = Number(evt.item?.dataset?.id);
                        const status = evt.to?.dataset?.status;
                        const idsInTarget = Array.from(evt.to?.querySelectorAll('.task-card') || [])
                            .map((item) => Number(item.dataset.id))
                            .filter((id) => Number.isInteger(id) && id > 0);

                        const componentEl = evt.to?.closest('[wire\\:id]');
                        if (!componentEl || !taskId || !status) return;

                        const component = Livewire.find(componentEl.getAttribute('wire:id'));
                        component?.call('moveTask', taskId, status, idsInTarget);
                    },
                }));
            };

            const initAll = () => columns.forEach((id) => initSortable(document.getElementById(id)));

            document.addEventListener('livewire:initialized', initAll);
            window.addEventListener('kanban-refresh-sortables', initAll);
            initAll();
        })();
    </script>
</div>