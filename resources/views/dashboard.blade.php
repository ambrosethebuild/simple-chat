@extends(config('simple-chat.layout', 'layouts.app'))

@section('title', __('simple-chat::messages.titles.dashboard'))
@section(config('simple-chat.section', 'content'))

    <div class="max-w-6xl mx-auto py-10 px-4 sm:px-6 lg:px-8">

        {{-- Header --}}
        <div class="flex justify-between items-center mb-8">
            <div class="flex items-center space-x-3">
                <x-tabler-layout-dashboard class="w-8 h-8 {{ config('simple-chat.theme.primary_text', 'text-indigo-600') }}" />
                <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">
                    {{ __('simple-chat::messages.titles.dashboard') }}
                </h1>
            </div>
            <a href="{{ route('simple-chat.index') }}"
               class="inline-flex items-center gap-1.5 px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors duration-200">
                <x-tabler-arrow-left class="w-4 h-4" />
                {{ __('simple-chat::messages.actions.back_to_messages') }}
            </a>
        </div>

        {{-- KPI Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">

            {{-- Total --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-start gap-4">
                <div class="flex-shrink-0 w-12 h-12 rounded-lg bg-indigo-50 flex items-center justify-center">
                    <x-tabler-ticket class="w-6 h-6 text-indigo-600" />
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">{{ __('simple-chat::messages.dashboard.total_tickets') }}</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ $totalTickets }}</p>
                </div>
            </div>

            {{-- Open --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-start gap-4">
                <div class="flex-shrink-0 w-12 h-12 rounded-lg bg-emerald-50 flex items-center justify-center">
                    <x-tabler-circle-dot class="w-6 h-6 text-emerald-600" />
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">{{ __('simple-chat::messages.dashboard.open_tickets') }}</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ $openTickets }}</p>
                </div>
            </div>

            {{-- Closed --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-start gap-4">
                <div class="flex-shrink-0 w-12 h-12 rounded-lg bg-gray-100 flex items-center justify-center">
                    <x-tabler-circle-check class="w-6 h-6 text-gray-500" />
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">{{ __('simple-chat::messages.dashboard.closed_tickets') }}</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ $closedTickets }}</p>
                </div>
            </div>

            {{-- Unassigned --}}
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

        {{-- Agent Matrix --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
                <x-tabler-users class="w-5 h-5 {{ config('simple-chat.theme.primary_text', 'text-indigo-600') }}" />
                <h2 class="text-lg font-semibold text-gray-800">{{ __('simple-chat::messages.dashboard.agent_matrix') }}</h2>
            </div>

            @if(count($agentStats) > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    <div class="flex items-center gap-1.5">
                                        <x-tabler-user class="w-4 h-4" />
                                        {{ __('simple-chat::messages.dashboard.agent') }}
                                    </div>
                                </th>
                                <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <x-tabler-ticket class="w-4 h-4" />
                                        {{ __('simple-chat::messages.dashboard.assigned') }}
                                    </div>
                                </th>
                                <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <x-tabler-circle-dot class="w-4 h-4" />
                                        {{ __('simple-chat::messages.dashboard.open') }}
                                    </div>
                                </th>
                                <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <x-tabler-circle-check class="w-4 h-4" />
                                        {{ __('simple-chat::messages.dashboard.closed') }}
                                    </div>
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <x-tabler-clock class="w-4 h-4" />
                                        {{ __('simple-chat::messages.dashboard.last_activity') }}
                                    </div>
                                </th>
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
                                @endphp
                                <tr class="hover:bg-gray-50 transition-colors duration-100">

                                    {{-- Agent --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-3">
                                            <div class="flex-shrink-0 w-9 h-9 rounded-full {{ config('simple-chat.theme.primary_color', 'bg-indigo-600') }} flex items-center justify-center text-white text-xs font-bold">
                                                {{ $initials }}
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">{{ $name }}</p>
                                                @if($email)
                                                    <p class="text-xs text-gray-400">{{ $email }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Assigned --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="inline-flex items-center justify-center min-w-[2rem] px-2 py-0.5 rounded-full text-sm font-semibold bg-indigo-50 text-indigo-700">
                                            {{ $stats['total'] }}
                                        </span>
                                    </td>

                                    {{-- Open --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="inline-flex items-center justify-center min-w-[2rem] px-2 py-0.5 rounded-full text-sm font-semibold {{ $stats['open'] > 0 ? 'bg-emerald-50 text-emerald-700' : 'bg-gray-50 text-gray-400' }}">
                                            {{ $stats['open'] }}
                                        </span>
                                    </td>

                                    {{-- Closed --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="inline-flex items-center justify-center min-w-[2rem] px-2 py-0.5 rounded-full text-sm font-semibold {{ $stats['closed'] > 0 ? 'bg-gray-100 text-gray-600' : 'bg-gray-50 text-gray-400' }}">
                                            {{ $stats['closed'] }}
                                        </span>
                                    </td>

                                    {{-- Last Activity --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <div class="text-sm text-gray-500 flex items-center justify-end gap-1.5">
                                            <x-tabler-clock class="w-4 h-4 text-gray-400" />
                                            @if($stats['last_activity'])
                                                <span title="{{ $stats['last_activity']->format('Y-m-d H:i') }}">
                                                    {{ $stats['last_activity']->diffForHumans() }}
                                                </span>
                                            @else
                                                <span class="text-gray-400">{{ __('simple-chat::messages.dashboard.never') }}</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>

                                {{-- Load bar row --}}
                                <tr class="bg-gray-50 border-b border-gray-100">
                                    <td colspan="5" class="px-6 pb-3 pt-0">
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

@endsection
