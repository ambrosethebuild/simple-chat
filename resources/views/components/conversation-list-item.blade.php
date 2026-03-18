@props(['conversation', 'mode' => 'direct', 'canAssignTickets' => false])

<li>
    <a href="{{ route('simple-chat.show', $conversation->id) }}"
        class="block hover:bg-gray-50 transition duration-150 ease-in-out">
        <div class="px-4 py-4 sm:px-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center flex-1 min-w-0">
                    <div class="flex-shrink-0">
                        <span
                            class="inline-block h-12 w-12 rounded-full overflow-hidden bg-gray-100 border border-gray-200 shadow-sm">
                            <svg class="h-full w-full text-gray-300 mt-2" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </span>
                    </div>
                    <div class="ml-4 flex-1 min-w-0">
                        <p
                            class="text-sm font-semibold {{ config('simple-chat.theme.primary_text', 'text-indigo-600') }} truncate">
                            {{ __('simple-chat::messages.labels.conversation') }} #{{ Str::limit($conversation->id, 8) }}
                        </p>
                        @if(config('simple-chat.mode') === 'support')
                            <div class="mt-1 flex flex-col text-sm text-gray-500 truncate">
                                {{ $conversation->other_users->first()->name ?? __('simple-chat::messages.status.unassigned') }}
                            </div>
                        @else
                            <div class="mt-1 flex flex-col">
                                <p class="text-sm text-gray-500 truncate">
                                    <span class="font-medium text-gray-700">{{ __('simple-chat::messages.labels.participants') }}:</span>
                                    {{ $conversation->participant_names }}
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="ml-4 flex-shrink-0 flex flex-col items-end space-y-2">
                    @if(isset($mode) && $mode === 'support' && isset($canAssignTickets) && $canAssignTickets)
                        @php
                            $parts = is_array($conversation->participants) ? $conversation->participants : json_decode($conversation->participants ?? '[]', true);
                            $isAssigned = is_array($parts) && count($parts) > 1;
                        @endphp
                        @if(!$isAssigned && $conversation->status !== 'closed' && !$conversation->trashed())
                            <form action="{{ route('simple-chat.assign', $conversation->id) }}" method="POST">
                                @csrf
                                <button type="submit"
                                    class="px-3 py-1.5 inline-flex text-xs leading-5 font-semibold rounded-md shadow-sm text-white {{ config('simple-chat.theme.primary_color', 'bg-indigo-600') }} {{ config('simple-chat.theme.primary_hover', 'hover:bg-indigo-700') }} focus:outline-none focus:ring-2 focus:ring-offset-2 {{ config('simple-chat.theme.primary_ring', 'focus:ring-indigo-500') }} transition-colors">
                                    {{ __('simple-chat::messages.actions.assign_to_me') }}
                                </button>
                            </form>
                        @endif
                    @endif

                    <div class="flex items-center space-x-2">
                        @if ($conversation->trashed())
                            <p
                                class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-medium rounded-full bg-gray-100 text-gray-800">
                                {{ __('simple-chat::messages.status.deleted_badge') }}
                            </p>
                        @elseif ($conversation->status === 'closed')
                            <p
                                class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-medium rounded-full bg-red-100 text-red-800">
                                {{ __('simple-chat::messages.status.closed_badge') }}
                            </p>
                        @else
                            <p
                                class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-medium rounded-full bg-green-100 text-green-800">
                                {{ ucfirst($conversation->status ?? __('simple-chat::messages.status.open')) }}
                            </p>
                        @endif
                        <p class="text-xs text-gray-400 whitespace-nowrap flex items-center">
                            <svg class="mr-1 h-3 w-3 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ $conversation->updated_at->diffForHumans() }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </a>
</li>