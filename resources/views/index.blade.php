@extends(config('simple-chat.layout', 'layouts.app'))

@section('title', config('simple-chat.titles.index', 'Messages'))
@section(config('simple-chat.section', 'content'))
    <div class="max-w-4xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">
                {!! isset($showingTrashed) && $showingTrashed ? 'Deleted Tickets' : config('simple-chat.titles.index', 'Messages') !!}</h1>
            <div class="flex items-center space-x-4">
                @if(config('simple-chat.mode') === 'support' && auth()->check() && config('simple-chat.support.delete_mode', 'soft') === 'soft' && auth()->user()->can(config('simple-chat.support.permissions.view_deleted_tickets', 'view-deleted-tickets')))
                    @if(isset($showingTrashed) && $showingTrashed)
                        <a href="{{ route('simple-chat.index') }}" class="text-sm text-indigo-600 hover:text-indigo-900 font-medium">View Active</a>
                    @else
                        <a href="{{ route('simple-chat.trashed') }}" class="text-sm text-gray-600 hover:text-gray-900 font-medium">View Trashed</a>
                    @endif
                @endif
                <a href="{{ route('simple-chat.create') }}"
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white {{ config('simple-chat.theme.primary_color', 'bg-indigo-600') }} {{ config('simple-chat.theme.primary_hover', 'hover:bg-indigo-700') }} focus:outline-none focus:ring-2 focus:ring-offset-2 {{ config('simple-chat.theme.primary_ring', 'focus:ring-indigo-500') }} transition-colors duration-200">
                    New Message
                </a>
            </div>
        </div>

        <div class="bg-white shadow overflow-hidden sm:rounded-md">
            <ul role="list" class="divide-y divide-gray-200">
                @forelse($conversations as $conversation)
                    @include('simple-chat::components.conversation-list-item', [
                        'conversation' => $conversation,
                        'mode' => $mode ?? 'direct',
                        'canAssignTickets' => $canAssignTickets ?? false
                    ])
                  @empty
                <div class="text-center py-10">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No conversations</h3>
                        <p class="mt-1 text-sm text-gray-500">Get started by starting a new chat.</p>
                    </div>
            @endforelse
                </ul>
            </div>
        </div>
@endsection