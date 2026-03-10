<div class="min-h-screen bg-slate-100 p-6" wire:loading.class="opacity-90 transition-opacity duration-200">
    <style>
        [x-cloak] { display: none !important; }
        .task-card { transition: transform 180ms ease, box-shadow 180ms ease, opacity 180ms ease; animation: cardIn 240ms ease-out; }
        .task-card:hover { transform: translateY(-2px); box-shadow: 0 14px 28px rgba(15, 23, 42, 0.12); }
        .drag-handle { cursor: grab; user-select: none; }
        .sortable-chosen { opacity: 0.86; }
        .sortable-drag { transform: rotate(1deg) scale(1.01); box-shadow: 0 14px 28px rgba(30, 41, 59, 0.24); }
        .kanban-ghost { background: #e0f2fe !important; border: 1px dashed #38bdf8; opacity: 0.65; }
        .column-drop-active { outline: 2px dashed #38bdf8; outline-offset: -4px; background: #f0f9ff; }
        @keyframes cardIn { from { opacity: 0; transform: translateY(6px); } to { opacity: 1; transform: translateY(0); } }
    </style>

    <div class="mx-auto max-w-7xl space-y-6">
        <div class="rounded-xl bg-white p-4 shadow-sm">
            <h1 class="text-xl font-bold text-slate-800">Kanban Internship Board</h1>
            <p class="mt-1 text-sm text-slate-500">Role: <span class="font-semibold uppercase">{{ $role }}</span></p>

            <div class="mt-4 grid grid-cols-1 gap-3 md:grid-cols-2">
                <div>
                    <label class="mb-1 block text-sm font-semibold text-slate-700">«Œ — «·ÿ«·»/«· œ—Ì»</label>
                    <select wire:model.live="selectedApplicationId" class="w-full rounded border border-slate-300 p-2">
                        @forelse($applicationOptions as $opt)
                            <option value="{{ $opt['id'] }}">
                                #{{ $opt['id'] }} - {{ $opt['student_name'] }} | {{ $opt['opportunity_title'] }}
                            </option>
                        @empty
                            <option value="">·« ÌÊÃœ  ÿ»Ìﬁ«  „ «Õ… ·ﬂ</option>
                        @endforelse
                    </select>
                </div>

                @if($currentApplication && $currentApplication->training_completed_at)
                    <div class="rounded border border-emerald-200 bg-emerald-50 p-3 text-sm text-emerald-800">
                        «· œ—Ì» „‰ ÂÌ. «·‰ ÌÃ… «·‰Â«∆Ì…: <strong>{{ $currentApplication->final_score ?? '-' }}/100</strong>
                    </div>
                @endif
            </div>
        </div>

        @if($this->canCreateTask() && $selectedApplicationId)
            <div class="rounded-xl bg-white p-4 shadow-sm">
                <h2 class="mb-3 text-lg font-semibold text-slate-800">≈÷«›… „Â„… ÃœÌœ… (›ﬁÿ ··‘—ﬂ…/«·„‘—›)</h2>
                <div class="grid grid-cols-1 gap-2 md:grid-cols-2">
                    <div>
                        <input wire:model.defer="title" placeholder="Task title" class="w-full rounded border border-slate-300 p-2">
                        @error('title') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <input wire:model.defer="due_date" type="date" class="w-full rounded border border-slate-300 p-2">
                        @error('due_date') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div class="md:col-span-2">
                        <textarea wire:model.defer="details" rows="3" placeholder="Task details" class="w-full rounded border border-slate-300 p-2"></textarea>
                        @error('details') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <select wire:model.defer="label" class="w-full rounded border border-slate-300 p-2">
                            <option value="">Label</option>
                            <option value="red">Urgent</option>
                            <option value="green">Low</option>
                            <option value="blue">Feature</option>
                        </select>
                    </div>
                </div>

                <button type="button" wire:click="addTask" class="mt-3 rounded bg-sky-600 px-4 py-2 text-white hover:bg-sky-700">Add Task</button>
            </div>
        @endif

        @if($selectedApplicationId)
            <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                @foreach(['todo' => 'Todo', 'progress' => 'In Progress', 'done' => 'Done'] as $key => $columnTitle)
                    <div class="rounded-xl border border-slate-200 bg-white p-3 shadow-sm">
                        <h2 class="mb-3 font-bold text-slate-700">{{ $columnTitle }} <span class="text-xs text-slate-400">({{ count($$key) }})</span></h2>
                        <div id="{{ $key }}Column" data-status="{{ $key }}" class="min-h-[320px] space-y-2 rounded bg-slate-100 p-2 transition-all duration-200">
                            @foreach($$key as $task)
                                <article class="task-card rounded bg-white p-3 shadow" data-id="{{ $task->id }}" wire:key="task-{{ $task->id }}">
                                    <div class="flex items-start justify-between gap-2">
                                        <div class="flex-1 space-y-1">
                                            <div class="flex items-center gap-2">
                                                @if($role === 'student')
                                                    <span class="drag-handle text-slate-400">::</span>
                                                @endif
                                                <span class="font-semibold text-slate-800">{{ $task->title }}</span>
                                            </div>
                                            <p class="text-xs text-slate-500">Created by: {{ $task->creator->name ?? '-' }} ({{ $task->creator->role ?? '-' }})</p>
                                            @if($task->details)
                                                <p class="text-sm text-slate-600">{{ $task->details }}</p>
                                            @endif
                                            @if($task->student_solution)
                                                <p class="rounded bg-emerald-50 p-2 text-xs text-emerald-800">Student Solution: {{ $task->student_solution }}</p>
                                            @endif
                                            <p class="text-xs text-slate-500">Due: {{ $task->due_date ? \Illuminate\Support\Carbon::parse($task->due_date)->format('Y-m-d') : '-' }}</p>
                                            <p class="text-xs text-slate-500">Task Score: {{ $task->company_score ?? 0 }} + {{ $task->supervisor_score ?? 0 }} / 100</p>
                                        </div>
                                        <div class="shrink-0 space-x-2">
                                            <button type="button" wire:click="editTask({{ $task->id }})" class="text-xs text-sky-600">Open</button>
                                            @if(in_array($role, ['company', 'supervisor'], true))
                                                <button type="button" onclick="if(confirm('Delete this task?')) { $wire.deleteTask({{ $task->id }}) }" class="text-xs text-red-600">Delete</button>
                                            @endif
                                        </div>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            @if(in_array($role, ['company', 'supervisor'], true))
                <div class="rounded-xl bg-white p-4 shadow-sm">
                    <h3 class="text-lg font-semibold text-slate-800">«· ﬁÌÌ„ «·‰Â«∆Ì ·· œ—Ì»</h3>
                    <div class="mt-2 grid grid-cols-1 gap-2 md:grid-cols-3">
                        <input type="number" min="0" max="100" wire:model.defer="finalRoleScore" class="rounded border border-slate-300 p-2" placeholder="⁄·«„ ﬂ «·‰Â«∆Ì… (0-100)">
                        <button type="button" wire:click="submitFinalRoleScore" class="rounded bg-slate-700 px-3 py-2 text-white">Õ›Ÿ ⁄·«„… {{ $role }}</button>
                        <button type="button" wire:click="finishTraining" class="rounded bg-emerald-600 px-3 py-2 text-white">≈‰Â«¡ «· œ—Ì»</button>
                    </div>
                    @error('finalRoleScore') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    @if($currentApplication)
                        <p class="mt-2 text-sm text-slate-600">Company: {{ $currentApplication->company_final_score ?? '-' }} | Supervisor: {{ $currentApplication->supervisor_final_score ?? '-' }} | Final: {{ $currentApplication->final_score ?? '-' }}/100</p>
                    @endif
                </div>
            @endif
        @endif
    </div>

    <div x-data="{ open: $wire.entangle('showEditPopup') }" x-show="open" x-cloak x-transition.opacity.duration.180ms class="fixed inset-0 z-50 flex items-center justify-center bg-black/45 p-3">
        <div x-show="open" x-transition:enter="transition ease-out duration-220" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-180" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="max-h-[90vh] w-full overflow-y-auto rounded-xl bg-white p-6 shadow-2xl md:w-2/3">
            <h2 class="mb-4 text-lg font-bold text-slate-800">Task Details</h2>

            @if(in_array($role, ['company', 'supervisor'], true))
                <input wire:model.defer="editTitle" class="mb-2 w-full rounded border border-slate-300 p-2" placeholder="Title">
                <textarea wire:model.defer="editDetails" rows="3" class="mb-2 w-full rounded border border-slate-300 p-2" placeholder="Details"></textarea>
                <div class="grid grid-cols-1 gap-2 md:grid-cols-2">
                    <input wire:model.defer="editDueDate" type="date" class="rounded border border-slate-300 p-2">
                    <select wire:model.defer="editLabel" class="rounded border border-slate-300 p-2">
                        <option value="">Label</option>
                        <option value="red">Urgent</option>
                        <option value="green">Low</option>
                        <option value="blue">Feature</option>
                    </select>
                </div>
            @endif

            @if($role === 'student')
                <label class="mb-1 mt-2 block text-sm font-semibold">«·Õ· (Solution)</label>
                <textarea wire:model.defer="editStudentSolution" rows="4" class="w-full rounded border border-slate-300 p-2" placeholder="√÷› «·Õ· Â‰«..."></textarea>
                <label class="mb-1 mt-2 block text-sm font-semibold">—›⁄ „·›«  «· ”·Ì„</label>
                <input type="file" wire:model="editAttachments" multiple class="w-full rounded border border-slate-300 p-2">
                @error('editAttachments.*') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            @endif

            @if($role === 'company')
                <label class="mb-1 mt-2 block text-sm font-semibold">⁄·«„… «·‘—ﬂ… (0-50)</label>
                <input type="number" min="0" max="50" wire:model.defer="editCompanyScore" class="w-full rounded border border-slate-300 p-2">
            @endif

            @if($role === 'supervisor')
                <label class="mb-1 mt-2 block text-sm font-semibold">⁄·«„… «·„‘—› (0-50)</label>
                <input type="number" min="0" max="50" wire:model.defer="editSupervisorScore" class="w-full rounded border border-slate-300 p-2">
            @endif

            <div class="mt-4 rounded border border-slate-200 p-3">
                <h3 class="text-sm font-semibold text-slate-700">«· ⁄·Ìﬁ«  („⁄ «”„ «·„—”·)</h3>
                <div class="mt-2 space-y-2">
                    @forelse($existingComments as $comment)
                        <div class="rounded bg-slate-50 p-2 text-xs text-slate-700">
                            <p class="font-semibold">{{ $comment['author'] }} ({{ $comment['author_role'] }})</p>
                            <p>{{ $comment['content'] }}</p>
                            <p class="text-[11px] text-slate-500">{{ $comment['created_at'] }}</p>
                        </div>
                    @empty
                        <p class="text-xs text-slate-500">·« ÌÊÃœ  ⁄·Ìﬁ«  »⁄œ.</p>
                    @endforelse
                </div>
                <div class="mt-2 flex gap-2">
                    <input wire:model.defer="newComment" class="w-full rounded border border-slate-300 p-2 text-sm" placeholder="«ﬂ »  ⁄·Ìﬁﬂ...">
                    <button type="button" wire:click="addComment" class="rounded bg-sky-600 px-3 py-2 text-white">≈—”«·</button>
                </div>
            </div>

            <div class="mt-4 rounded border border-slate-200 p-3">
                <h3 class="text-sm font-semibold text-slate-700">„—›ﬁ«  «·ÿ«·»</h3>
                <ul class="mt-2 space-y-1">
                    @forelse($existingAttachments as $attachment)
                        <li>
                            <a href="{{ asset('storage/' . $attachment['filepath']) }}" target="_blank" class="text-xs text-sky-600 underline">
                                {{ $attachment['filename'] }}
                            </a>
                            <span class="text-xs text-slate-500"> - {{ $attachment['author'] }} ({{ $attachment['author_role'] }})</span>
                        </li>
                    @empty
                        <li class="text-xs text-slate-500">·« ÌÊÃœ „—›ﬁ«  »⁄œ.</li>
                    @endforelse
                </ul>
            </div>

            <div class="mt-4 flex justify-end gap-2">
                <button type="button" wire:click="updateTask" class="rounded bg-sky-600 px-4 py-2 text-white">Save</button>
                <button type="button" wire:click="closeEditPopup" class="rounded bg-slate-500 px-4 py-2 text-white">Close</button>
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
