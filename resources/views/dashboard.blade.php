@extends(config('simple-chat.layout', 'layouts.app'))

@section('title', __('simple-chat::messages.titles.dashboard'))
@section(config('simple-chat.section', 'content'))

    @php
        $primary      = config('simple-chat.theme.primary_color',  'bg-indigo-600');
        $primaryText  = config('simple-chat.theme.primary_text',   'text-indigo-600');
        $primaryHover = config('simple-chat.theme.primary_hover',  'hover:bg-indigo-700');
    @endphp

    <div class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8 space-y-8">

        {{-- ── Header ──────────────────────────────────────────────────── --}}
        <div class="flex justify-between items-center">
            <div class="flex items-center space-x-3">
                <x-tabler-layout-dashboard class="w-8 h-8 {{ $primaryText }}" />
                <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">
                    {{ __('simple-chat::messages.titles.dashboard') }}
                </h1>
                @if($isSuperAdmin)
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-violet-100 text-violet-700">
                        <x-tabler-shield-check class="w-3.5 h-3.5" />
                        Super Admin
                    </span>
                @endif
            </div>
            <a href="{{ route('simple-chat.index') }}"
               class="inline-flex items-center gap-1.5 px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                <x-tabler-arrow-left class="w-4 h-4" />
                {{ __('simple-chat::messages.actions.back_to_messages') }}
            </a>
        </div>

        {{-- ── KPI Row 1 ────────────────────────────────────────────────── --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-start gap-4">
                <div class="flex-shrink-0 w-12 h-12 rounded-lg bg-indigo-50 flex items-center justify-center">
                    <x-tabler-ticket class="w-6 h-6 text-indigo-600" />
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">{{ __('simple-chat::messages.dashboard.total_tickets') }}</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ $totalTickets }}</p>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-start gap-4">
                <div class="flex-shrink-0 w-12 h-12 rounded-lg bg-emerald-50 flex items-center justify-center">
                    <x-tabler-circle-dot class="w-6 h-6 text-emerald-600" />
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">{{ __('simple-chat::messages.dashboard.open_tickets') }}</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ $openTickets }}</p>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-start gap-4">
                <div class="flex-shrink-0 w-12 h-12 rounded-lg bg-gray-100 flex items-center justify-center">
                    <x-tabler-circle-check class="w-6 h-6 text-gray-500" />
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">{{ __('simple-chat::messages.dashboard.closed_tickets') }}</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ $closedTickets }}</p>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-start gap-4">
                <div class="flex-shrink-0 w-12 h-12 rounded-lg bg-amber-50 flex items-center justify-center">
                    <x-tabler-user-question class="w-6 h-6 text-amber-600" />
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">{{ __('simple-chat::messages.dashboard.unassigned_tickets') }}</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ $unassignedTickets }}</p>
                </div>
            </div>

        </div>

        {{-- ── KPI Row 2 ────────────────────────────────────────────────── --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-start gap-4">
                <div class="flex-shrink-0 w-12 h-12 rounded-lg bg-sky-50 flex items-center justify-center">
                    <x-tabler-calendar class="w-6 h-6 text-sky-600" />
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">{{ __('simple-chat::messages.dashboard.today_tickets') }}</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ $todayTickets }}</p>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-start gap-4">
                <div class="flex-shrink-0 w-12 h-12 rounded-lg bg-violet-50 flex items-center justify-center">
                    <x-tabler-calendar-week class="w-6 h-6 text-violet-600" />
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">{{ __('simple-chat::messages.dashboard.week_tickets') }}</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ $weekTickets }}</p>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-start gap-4">
                <div class="flex-shrink-0 w-12 h-12 rounded-lg bg-teal-50 flex items-center justify-center">
                    <x-tabler-chart-pie class="w-6 h-6 text-teal-600" />
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">{{ __('simple-chat::messages.dashboard.resolution_rate') }}</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ $resolutionRate }}<span class="text-lg font-medium text-gray-400">%</span></p>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-start gap-4">
                <div class="flex-shrink-0 w-12 h-12 rounded-lg bg-rose-50 flex items-center justify-center">
                    <x-tabler-trash class="w-6 h-6 text-rose-500" />
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">{{ __('simple-chat::messages.dashboard.deleted_tickets') }}</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ $deletedTickets }}</p>
                </div>
            </div>

        </div>

        {{-- ── Charts Row ───────────────────────────────────────────────── --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

            {{-- Line chart: tickets per day --}}
            <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center gap-2 mb-4">
                    <x-tabler-chart-line class="w-5 h-5 {{ $primaryText }}" />
                    <h2 class="text-base font-semibold text-gray-800">{{ __('simple-chat::messages.dashboard.tickets_over_time') }}</h2>
                </div>
                <div class="relative h-56">
                    <canvas id="sc-line-chart"></canvas>
                </div>
            </div>

            {{-- Donut chart: status breakdown --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center gap-2 mb-4">
                    <x-tabler-chart-donut class="w-5 h-5 {{ $primaryText }}" />
                    <h2 class="text-base font-semibold text-gray-800">{{ __('simple-chat::messages.dashboard.status_breakdown') }}</h2>
                </div>
                <div class="relative h-56 flex items-center justify-center">
                    <canvas id="sc-donut-chart"></canvas>
                </div>
                {{-- Legend --}}
                <div class="mt-4 flex justify-center gap-6 text-sm text-gray-600">
                    <span class="flex items-center gap-1.5"><span class="inline-block w-3 h-3 rounded-full bg-emerald-400"></span> {{ __('simple-chat::messages.dashboard.open') }}</span>
                    <span class="flex items-center gap-1.5"><span class="inline-block w-3 h-3 rounded-full bg-gray-400"></span> {{ __('simple-chat::messages.dashboard.closed') }}</span>
                </div>
            </div>

        </div>

        {{-- ── Recent Conversations ─────────────────────────────────────── --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <x-tabler-messages class="w-5 h-5 {{ $primaryText }}" />
                    <h2 class="text-lg font-semibold text-gray-800">{{ __('simple-chat::messages.dashboard.recent_conversations') }}</h2>
                </div>
                <a href="{{ route('simple-chat.index') }}"
                   class="text-sm {{ $primaryText }} hover:underline flex items-center gap-1">
                    {{ __('simple-chat::messages.dashboard.view_all') }}
                    <x-tabler-arrow-right class="w-4 h-4" />
                </a>
            </div>

            @if($recentConversations->count() > 0)
                <div class="divide-y divide-gray-50">
                    @foreach($recentConversations as $conv)
                        @php
                            $parts = is_array($conv->participants)
                                ? $conv->participants
                                : json_decode($conv->participants ?? '[]', true);
                            $participantCount = count($parts ?? []);
                            $isClosed  = $conv->status === 'closed';
                            $isTrashed = $conv->trashed();
                        @endphp
                        <div class="px-6 py-4 flex items-center justify-between hover:bg-gray-50 transition-colors">
                            <div class="flex items-center gap-4 min-w-0">
                                <div class="flex-shrink-0 w-9 h-9 rounded-full bg-gray-100 flex items-center justify-center">
                                    <x-tabler-message class="w-4 h-4 text-gray-500" />
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">
                                        {{ __('simple-chat::messages.labels.conversation') }} #{{ Str::limit($conv->id, 8, '') }}
                                    </p>
                                    <p class="text-xs text-gray-400 mt-0.5">
                                        <x-tabler-users class="w-3 h-3 inline mr-0.5" />
                                        {{ trans('simple-chat::messages.dashboard.participants_count', ['count' => $participantCount]) }}
                                        &middot;
                                        <x-tabler-clock class="w-3 h-3 inline mr-0.5" />
                                        {{ $conv->updated_at->diffForHumans() }}
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3 flex-shrink-0 ml-4">
                                @if($isTrashed)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-rose-50 text-rose-600">
                                        <x-tabler-trash class="w-3 h-3" />{{ __('simple-chat::messages.status.deleted_badge') }}
                                    </span>
                                @elseif($isClosed)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-500">
                                        <x-tabler-circle-check class="w-3 h-3" />{{ __('simple-chat::messages.status.closed_badge') }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700">
                                        <x-tabler-circle-dot class="w-3 h-3" />{{ __('simple-chat::messages.status.open') }}
                                    </span>
                                @endif
                                <a href="{{ route('simple-chat.show', $conv->id) }}"
                                   class="inline-flex items-center gap-1 text-xs font-medium {{ $primaryText }} hover:underline">
                                    {{ __('simple-chat::messages.actions.view_conversation') }}
                                    <x-tabler-arrow-right class="w-3 h-3" />
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-10">
                    <x-tabler-messages class="w-10 h-10 text-gray-300 mx-auto" />
                    <p class="mt-2 text-sm text-gray-500">{{ __('simple-chat::messages.dashboard.no_recent') }}</p>
                </div>
            @endif
        </div>

        {{-- ── Agent Matrix ─────────────────────────────────────────────── --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
                <x-tabler-users class="w-5 h-5 {{ $primaryText }}" />
                <h2 class="text-lg font-semibold text-gray-800">{{ __('simple-chat::messages.dashboard.agent_matrix') }}</h2>
            </div>

            @if(count($agentStats) > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    <div class="flex items-center gap-1.5"><x-tabler-user class="w-4 h-4" />{{ __('simple-chat::messages.dashboard.agent') }}</div>
                                </th>
                                <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    <div class="flex items-center justify-center gap-1.5"><x-tabler-ticket class="w-4 h-4" />{{ __('simple-chat::messages.dashboard.assigned') }}</div>
                                </th>
                                <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    <div class="flex items-center justify-center gap-1.5"><x-tabler-circle-dot class="w-4 h-4" />{{ __('simple-chat::messages.dashboard.open') }}</div>
                                </th>
                                <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    <div class="flex items-center justify-center gap-1.5"><x-tabler-circle-check class="w-4 h-4" />{{ __('simple-chat::messages.dashboard.closed') }}</div>
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    <div class="flex items-center justify-end gap-1.5"><x-tabler-clock class="w-4 h-4" />{{ __('simple-chat::messages.dashboard.last_activity') }}</div>
                                </th>
                                @if($isSuperAdmin)
                                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <x-tabler-shield-check class="w-4 h-4 text-violet-500" />
                                            Actions
                                        </div>
                                    </th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @foreach($agentStats as $agentId => $stats)
                                @php
                                    $agent    = $agents->get($agentId);
                                    $name     = $agent?->name ?? $agentId;
                                    $email    = $agent?->email ?? null;
                                    $initials = collect(explode(' ', $name))
                                        ->map(fn($w) => strtoupper(substr($w, 0, 1)))
                                        ->take(2)->implode('');
                                    $openRatio = $stats['total'] > 0
                                        ? round(($stats['open'] / $stats['total']) * 100)
                                        : 0;
                                    $agentConvs = $agentConversations[$agentId] ?? [];
                                    $colSpan = $isSuperAdmin ? 6 : 5;
                                @endphp
                                <tr class="hover:bg-gray-50 transition-colors duration-100">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-3">
                                            <div class="flex-shrink-0 w-9 h-9 rounded-full {{ $primary }} flex items-center justify-center text-white text-xs font-bold">
                                                {{ $initials }}
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">{{ $name }}</p>
                                                @if($email)<p class="text-xs text-gray-400">{{ $email }}</p>@endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="inline-flex items-center justify-center min-w-[2rem] px-2 py-0.5 rounded-full text-sm font-semibold bg-indigo-50 text-indigo-700">{{ $stats['total'] }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="inline-flex items-center justify-center min-w-[2rem] px-2 py-0.5 rounded-full text-sm font-semibold {{ $stats['open'] > 0 ? 'bg-emerald-50 text-emerald-700' : 'bg-gray-50 text-gray-400' }}">{{ $stats['open'] }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="inline-flex items-center justify-center min-w-[2rem] px-2 py-0.5 rounded-full text-sm font-semibold {{ $stats['closed'] > 0 ? 'bg-gray-100 text-gray-600' : 'bg-gray-50 text-gray-400' }}">{{ $stats['closed'] }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <div class="text-sm text-gray-500 flex items-center justify-end gap-1.5">
                                            <x-tabler-clock class="w-4 h-4 text-gray-400" />
                                            @if($stats['last_activity'])
                                                <span title="{{ $stats['last_activity']->format('Y-m-d H:i') }}">{{ $stats['last_activity']->diffForHumans() }}</span>
                                            @else
                                                <span class="text-gray-400">{{ __('simple-chat::messages.dashboard.never') }}</span>
                                            @endif
                                        </div>
                                    </td>
                                    @if($isSuperAdmin)
                                        <td class="px-6 py-4 whitespace-nowrap text-right">
                                            @if(count($agentConvs) > 0)
                                                <button type="button"
                                                        onclick="document.getElementById('sc-unassign-{{ $agentId }}').classList.toggle('hidden')"
                                                        class="inline-flex items-center gap-1 text-xs font-medium text-violet-600 hover:text-violet-800">
                                                    <x-tabler-list-details class="w-3.5 h-3.5" />
                                                    {{ __('simple-chat::messages.dashboard.unassign_agent') }} ({{ count($agentConvs) }})
                                                </button>
                                            @endif
                                        </td>
                                    @endif
                                </tr>

                                {{-- Super-admin: expandable per-conversation unassign panel --}}
                                @if($isSuperAdmin && count($agentConvs) > 0)
                                    <tr id="sc-unassign-{{ $agentId }}" class="hidden bg-violet-50 border-b border-violet-100">
                                        <td colspan="{{ $colSpan }}" class="px-8 py-3">
                                            <p class="text-xs font-semibold text-violet-700 mb-2 flex items-center gap-1">
                                                <x-tabler-shield-check class="w-3.5 h-3.5" />
                                                Conversations assigned to {{ $name }}
                                            </p>
                                            <div class="space-y-1.5">
                                                @foreach($agentConvs as $agentConv)
                                                    <div class="flex items-center justify-between bg-white rounded-lg px-3 py-2 border border-violet-100">
                                                        <div class="flex items-center gap-2 text-xs text-gray-600">
                                                            <x-tabler-message class="w-3.5 h-3.5 text-gray-400" />
                                                            <span class="font-medium text-gray-800">#{{ Str::limit($agentConv->id, 8, '') }}</span>
                                                            <span class="{{ $agentConv->status === 'closed' ? 'text-gray-400' : 'text-emerald-600' }}">
                                                                {{ $agentConv->status === 'closed' ? __('simple-chat::messages.status.closed_badge') : __('simple-chat::messages.status.open') }}
                                                            </span>
                                                            <span class="text-gray-400">&middot; {{ $agentConv->updated_at->diffForHumans() }}</span>
                                                        </div>
                                                        <div class="flex items-center gap-2">
                                                            <a href="{{ route('simple-chat.show', $agentConv->id) }}"
                                                               class="text-xs {{ $primaryText }} hover:underline flex items-center gap-0.5">
                                                                <x-tabler-eye class="w-3 h-3" />View
                                                            </a>
                                                            @if($agentConv->status !== 'closed' && !$agentConv->trashed())
                                                                <form method="POST"
                                                                      action="{{ route('simple-chat.unassign', [$agentConv->id, $agentId]) }}"
                                                                      onsubmit="return confirm('{{ __('simple-chat::messages.dashboard.confirm_unassign') }}')"
                                                                      class="inline">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit"
                                                                            class="inline-flex items-center gap-0.5 text-xs font-medium text-rose-600 hover:text-rose-800">
                                                                        <x-tabler-user-minus class="w-3 h-3" />
                                                                        {{ __('simple-chat::messages.dashboard.unassign_agent') }}
                                                                    </button>
                                                                </form>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </td>
                                    </tr>
                                @endif

                                {{-- Load bar --}}
                                <tr class="bg-gray-50 border-b border-gray-100">
                                    <td colspan="{{ $colSpan }}" class="px-6 pb-3 pt-0">
                                        <div class="flex items-center gap-2">
                                            <div class="flex-1 h-1.5 bg-gray-200 rounded-full overflow-hidden">
                                                <div class="h-full rounded-full {{ $openRatio > 70 ? 'bg-amber-400' : 'bg-emerald-400' }} transition-all duration-500"
                                                     style="width: {{ $openRatio }}%"></div>
                                            </div>
                                            <span class="text-xs text-gray-400 whitespace-nowrap">{{ $openRatio }}% open</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-14">
                    <x-tabler-users-group class="w-12 h-12 text-gray-300 mx-auto" />
                    <p class="mt-3 text-sm text-gray-500">{{ __('simple-chat::messages.dashboard.no_agents') }}</p>
                </div>
            @endif
        </div>

    </div>

    {{-- ── Chart.js ─────────────────────────────────────────────────────── --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
    <script>
        (function () {
            // ── Data from PHP ────────────────────────────────────────────
            const lineLabels = @json($ticketsPerDay->keys());
            const lineData   = @json($ticketsPerDay->values());
            const openCount   = {{ $openTickets }};
            const closedCount = {{ $closedTickets }};

            // ── Line chart ───────────────────────────────────────────────
            const lineCtx = document.getElementById('sc-line-chart');
            if (lineCtx) {
                new Chart(lineCtx, {
                    type: 'line',
                    data: {
                        labels: lineLabels,
                        datasets: [{
                            label: '{{ __('simple-chat::messages.dashboard.total_tickets') }}',
                            data: lineData,
                            fill: true,
                            tension: 0.4,
                            borderColor: '#6366f1',
                            backgroundColor: 'rgba(99,102,241,0.08)',
                            pointBackgroundColor: '#6366f1',
                            pointRadius: 3,
                            pointHoverRadius: 5,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            x: {
                                ticks: {
                                    maxTicksLimit: 10,
                                    font: { size: 11 },
                                    color: '#9ca3af',
                                },
                                grid: { display: false },
                            },
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1,
                                    font: { size: 11 },
                                    color: '#9ca3af',
                                },
                                grid: { color: 'rgba(0,0,0,0.04)' },
                            }
                        }
                    }
                });
            }

            // ── Donut chart ──────────────────────────────────────────────
            const donutCtx = document.getElementById('sc-donut-chart');
            if (donutCtx) {
                new Chart(donutCtx, {
                    type: 'doughnut',
                    data: {
                        labels: [
                            '{{ __('simple-chat::messages.dashboard.open') }}',
                            '{{ __('simple-chat::messages.dashboard.closed') }}',
                        ],
                        datasets: [{
                            data: [openCount, closedCount],
                            backgroundColor: ['#34d399', '#9ca3af'],
                            borderWidth: 2,
                            borderColor: '#fff',
                            hoverOffset: 6,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '68%',
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: ctx => ` ${ctx.label}: ${ctx.raw}`
                                }
                            }
                        }
                    }
                });
            }
        })();
    </script>

@endsection
