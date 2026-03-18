@extends(config('simple-chat.layout', 'layouts.app'))

@section('title', config('simple-chat.titles.show', __('simple-chat::messages.titles.show')) . ' #' . substr($conversation->id, 0, 8))
@section(config('simple-chat.section', 'content'))
    <div class="max-w-4xl mx-auto py-4 sm:px-6 lg:px-8 flex flex-col" style="height: calc(100vh - 100px);">
        <!-- Header -->
        <div
            class="bg-white shadow px-4 py-4 sm:px-6 flex justify-between items-center rounded-t-lg shrink-0 border border-gray-200">
            <div class="flex items-center">
                <a href="{{ route('simple-chat.index') }}" class="mr-4 text-gray-400 hover:text-gray-500">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <h1 class="text-lg font-medium text-gray-900">
                    {!! config('simple-chat.titles.show', __('simple-chat::messages.titles.show')) !!}
                    #{{ substr($conversation->id, 0, 8) }}
                </h1>
            </div>
            <div>
                <p class="text-sm text-gray-500">{{ $conversation->updated_at->diffForHumans() }}</p>
                <div class="flex space-x-2">
                    @if($conversation->status != 'closed' && !$conversation->trashed())
                        @can("close-ticket")
                            <button type="button"
                                class="mt-1 inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors"
                                onclick="document.getElementById('sc-close-modal').classList.remove('hidden')">
                                {{ __('simple-chat::messages.actions.close_ticket') }}
                            </button>
                        @endcan
                    @endif
                    @if(config('simple-chat.mode') === 'support' && auth()->check() && auth()->user()->can(config('simple-chat.support.permissions.delete_ticket', 'delete-ticket')))
                        @if($conversation->trashed())
                            <button type="button"
                                class="mt-1 inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors"
                                onclick="document.getElementById('sc-restore-modal').classList.remove('hidden')">
                                {{ __('simple-chat::messages.actions.restore_ticket') }}
                            </button>
                            <button type="button"
                                class="mt-1 inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded shadow-sm text-white bg-gray-600 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors"
                                onclick="document.getElementById('sc-delete-modal').classList.remove('hidden')">
                                {{ __('simple-chat::messages.actions.permanent_delete') }}
                            </button>
                        @else
                            <button type="button"
                                class="mt-1 inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded shadow-sm text-white bg-gray-600 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors"
                                onclick="document.getElementById('sc-delete-modal').classList.remove('hidden')">
                                {{ __('simple-chat::messages.actions.delete_ticket') }}
                            </button>
                        @endif
                    @endif
                </div>
            </div>
        </div>

        {{-- participants: to indicate if agent assigned to conversation or not --}}
        @if(config('simple-chat.mode') === 'support')
            <div class="bg-white shadow px-4 py-4 sm:px-6 rounded-b-lg shrink-0 border border-gray-200 flex">
                @if($conversation->other_users->count() > 0)
                    @foreach ($conversation->other_users as $participant)
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center">
                                <span class="text-gray-600">{{ $participant->name[0] }}</span>
                            </div>
                            <div>
                                <h1 class="text-lg font-medium text-gray-900 ml-2">
                                    {{ $participant->name }}
                                </h1>
                                {{-- joined at --}}
                                @php
                                    $supportPermissions = config('simple-chat.support.permissions');
                                    $noneOfTheSupportPermissions = collect($supportPermissions)->every(function ($permission) {
                                        return !auth()->user()->can($permission);
                                    });
                                @endphp
                                @if($noneOfTheSupportPermissions == false)
                                    <p class="text-sm text-gray-400 ml-2">
                                        {{ $participant->created_at->diffForHumans() }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="text-center text-gray-500 italic w-full">
                        <p>{{ __('simple-chat::messages.status.unassigned') }}</p>
                        <p class="text-sm text-gray-400">{{ __('simple-chat::messages.status.unassigned_desc') }}</p>
                    </div>
                @endif
            </div>
        @endif

        <div class="flex-1 overflow-y-auto scroll-smooth space-y-4 py-4 px-2 border-l border-gray-200 border-r"
            id="sc-messages-container">
            @php $lastDate = null; @endphp
            @forelse($messages as $message)
                @php
                    $createdAt = \Carbon\Carbon::parse($message->created_at);
                    $messageDate = $createdAt->format('Y-m-d');
                    $displayDate = $createdAt->isToday() ? __('simple-chat::messages.date.today') : ($createdAt->isYesterday() ? __('simple-chat::messages.date.yesterday') : $createdAt->format('M j, Y'));
                @endphp

                @if($lastDate !== $messageDate)
                    <div class="flex justify-center my-4 sticky top-0 z-10 date-indicator" data-date="{{ $messageDate }}">
                        <span
                            class="px-3 py-1 bg-gray-100/80 backdrop-blur-sm text-gray-500 text-xs rounded-full shadow-sm border border-gray-200">
                            {{ $displayDate }}
                        </span>
                    </div>
                    @php $lastDate = $messageDate; @endphp
                @endif
                @include('simple-chat::components.message-item', ['message' => $message])
            @empty
                @include('simple-chat::components.empty-messages')
            @endforelse
        </div>

        <!-- Input -->
        <div class="bg-white shadow px-4 py-4 sm:px-6 rounded-b-lg shrink-0 border border-gray-200">
            @if(isset($canReply) && !$canReply)
                <div class="text-center text-sm text-gray-500 py-2">
                    @if($conversation->trashed())
                        <div class="bg-gray-50 px-4 py-12 sm:px-6 flex justify-center items-center">
                            <p class="text-gray-500 font-medium italic flex items-center">
                                <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                                {{ __('simple-chat::messages.status.deleted') }}
                            </p>
                        </div>
                    @elseif($conversation->status === 'closed')
                        <div class="bg-gray-50 px-4 py-12 sm:px-6 flex justify-center items-center">
                            <p class="text-gray-500 font-medium italic flex items-center">
                                <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                                {{ __('simple-chat::messages.status.closed') }}
                            </p>
                        </div>
                    @else
                        <div class="bg-gray-50 px-4 py-12 sm:px-6 flex justify-center items-center">
                            <p class="text-gray-500 font-medium italic flex items-center text-center">
                                <svg class="h-5 w-5 mr-2 inline-block" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                {{ __('simple-chat::messages.status.no_permission') }}
                            </p>
                        </div>
                    @endif
                </div>
            @else
                <form id="sc-message-form" class="flex w-full" onsubmit="sendMessage(event)">
                    <div class="flex items-center w-full space-x-2">
                        <div class="flex-1 bg-gray-50 rounded-lg p-2 flex items-center">
                            @if(isset($editor) && $editor === 'wysiwyg')
                                <div id="sc-quill-editor" class="flex-1 min-h-[40px] max-h-[150px] overflow-y-auto"></div>
                                <input type="hidden" name="content" id="sc-content">
                            @else
                                <textarea name="content" id="sc-content" rows="1"
                                    class="flex-1 bg-transparent border-none focus:ring-0 text-gray-900 placeholder-gray-500 resize-none py-1 block w-full sm:text-sm"
                                    placeholder="{{ __('simple-chat::messages.placeholders.type_message') }}" required></textarea>
                            @endif
                        </div>
                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white {{ config('simple-chat.theme.primary_color', 'bg-indigo-600') }} {{ config('simple-chat.theme.primary_hover', 'hover:bg-indigo-700') }} focus:outline-none focus:ring-2 focus:ring-offset-2 {{ config('simple-chat.theme.primary_ring', 'focus:ring-indigo-500') }} transition-colors">
                            {{ __('simple-chat::messages.actions.send') }}
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>

    <!-- Close Confirmation Modal -->
    <div id="sc-close-modal" class="fixed z-10 inset-0 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog"
        aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"
                onclick="document.getElementById('sc-close-modal').classList.add('hidden')"></div>

            <!-- Modal panel -->
            <div
                class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                <div class="sm:flex sm:items-start">
                    <div
                        class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                        <!-- Heroicon: exclamation -->
                        <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                            {{ __('simple-chat::messages.modals.close_title') }}
                        </h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">
                                {{ __('simple-chat::messages.modals.close_desc') }}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                    <form action="{{ route('simple-chat.close', $conversation->id) }}" method="POST">
                        @csrf
                        <button type="submit"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                            {{ __('simple-chat::messages.actions.confirm_close') }}
                        </button>
                    </form>
                    <button type="button"
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm"
                        onclick="document.getElementById('sc-close-modal').classList.add('hidden')">
                        {{ __('simple-chat::messages.actions.cancel') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="sc-delete-modal" class="fixed z-10 inset-0 overflow-y-auto hidden" aria-labelledby="modal-title-delete"
        role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"
                onclick="document.getElementById('sc-delete-modal').classList.add('hidden')"></div>

            <!-- Modal panel -->
            <div
                class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                <div class="sm:flex sm:items-start">
                    <div
                        class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                        <!-- Heroicon: trash -->
                        <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title-delete">
                            @if($conversation->trashed())
                                {{ __('simple-chat::messages.modals.delete_permanent_title') }}
                            @else
                                {{ __('simple-chat::messages.modals.delete_title') }}
                            @endif
                        </h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">
                                @if($conversation->trashed())
                                    {{ __('simple-chat::messages.modals.delete_permanent_desc') }}
                                @else
                                    {{ __('simple-chat::messages.modals.delete_desc') }}
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
                <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                    <form action="{{ route('simple-chat.destroy', $conversation->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                            {{ __('simple-chat::messages.actions.confirm_delete') }}
                        </button>
                    </form>
                    <button type="button"
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm"
                        onclick="document.getElementById('sc-delete-modal').classList.add('hidden')">
                        {{ __('simple-chat::messages.actions.cancel') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Restore Confirmation Modal -->
    <div id="sc-restore-modal" class="fixed z-10 inset-0 overflow-y-auto hidden" aria-labelledby="modal-title-restore"
        role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"
                onclick="document.getElementById('sc-restore-modal').classList.add('hidden')"></div>

            <!-- Modal panel -->
            <div
                class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                <div class="sm:flex sm:items-start">
                    <div
                        class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                        <!-- Heroicon: arrow-uturn-left -->
                        <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title-restore">
                            {{ __('simple-chat::messages.modals.restore_title') }}
                        </h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">
                                {{ __('simple-chat::messages.modals.restore_desc') }}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                    <form action="{{ route('simple-chat.restore', $conversation->id) }}" method="POST">
                        @csrf
                        <button type="submit"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
                            {{ __('simple-chat::messages.actions.confirm_restore') }}
                        </button>
                    </form>
                    <button type="button"
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm"
                        onclick="document.getElementById('sc-restore-modal').classList.add('hidden')">
                        {{ __('simple-chat::messages.actions.cancel') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Templates for Realtime Appending -->
    <template id="sc-template-me">
        <div class="flex justify-end message-item">
            <div
                class="max-w-xs lg:max-w-md px-4 py-2 rounded-lg shadow-sm {{ config('simple-chat.theme.primary_color', 'bg-indigo-600') }} text-white rounded-br-none">
                <div class="sc-msg-content text-sm"></div>
                <p class="sc-msg-time text-xs mt-1 text-indigo-200"></p>
            </div>
        </div>
    </template>

    <template id="sc-template-other">
        <div class="flex justify-start message-item">
            <div class="max-w-xs lg:max-w-md px-4 py-2 rounded-lg shadow-sm bg-white text-gray-900 rounded-bl-none">
                <div class="sc-msg-content text-sm"></div>
                <p class="sc-msg-time text-xs mt-1 text-gray-400"></p>
            </div>
        </div>
    </template>

    @if(isset($editor) && $editor === 'wysiwyg')
        <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
        <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
        <style>
            #sc-quill-editor {
                border: none !important;
            }

            .ql-toolbar {
                border: none !important;
                border-bottom: 1px solid #e5e7eb !important;
                background: #f9fafb;
                padding: 4px 8px !important;
            }

            .ql-container {
                font-family: inherit;
                font-size: inherit;
            }
        </style>
    @endif

    @if($chatConfig['driver'] === 'appwrite')
        <script src="https://cdn.jsdelivr.net/npm/appwrite@13.0.0"></script>
    @elseif($chatConfig['driver'] === 'supabase')
        <script src="https://cdn.jsdelivr.net/npm/@supabase/supabase-js@2"></script>
    @endif
    <script>
        const container = document.getElementById('sc-messages-container');
        const form = document.getElementById('sc-message-form');
        const input = document.getElementById('sc-content');
        let quill = null;
        const useWysiwyg = {{ isset($editor) && $editor === 'wysiwyg' ? 'true' : 'false' }};

        const translations = {
            today: "{{ __('simple-chat::messages.date.today') }}",
            yesterday: "{{ __('simple-chat::messages.date.yesterday') }}",
            failedToSend: "{{ __('simple-chat::messages.alerts.failed_to_send') }}",
            errorSending: "{{ __('simple-chat::messages.alerts.error_sending') }}",
        };

        if (useWysiwyg) {
            quill = new Quill('#sc-quill-editor', {
                theme: 'snow',
                placeholder: '{{ __('simple-chat::messages.placeholders.type_message') }}',
                modules: {
                    toolbar: [
                        ['bold', 'italic', 'underline', 'strike'],
                        ['link'],
                        [{ 'list': 'bullet' }]
                    ]
                }
            });

            // Sync quill content to hidden input immediately on change
            quill.on('text-change', function () {
                input.value = quill.root.innerHTML === '<p><br></p>' ? '' : quill.root.innerHTML;
            });
        } else {
            // Simple textarea submit on Enter logic (prevent shift+enter)
            input.addEventListener('keydown', function (e) {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    if (this.value.trim()) {
                        form.requestSubmit ? form.requestSubmit() : form.dispatchEvent(new Event('submit', { cancelable: true }));
                    }
                }
            });
        }

        const conversationId = "{{ $conversation->id }}";
        const currentUserId = "{{ auth()->id() }}";
        const fetchUrl = "{{ route('simple-chat.messages.fetch', $conversation->id) }}";
        const storeUrl = "{{ route('simple-chat.messages.store', $conversation->id) }}";
        const csrfToken = "{{ csrf_token() }}";
        const chatConfig = @json($chatConfig);

        let knownIds = new Set();
        let lastAppendedDate = "{{ $lastDate }}";

        // Initialize known IDs
        document.querySelectorAll('.message-item').forEach(el => {
            knownIds.add(parseInt(el.dataset.id) || el.dataset.id);
        });

        function scrollToBottom() {
            setTimeout(() => {
                container.scrollTop = container.scrollHeight;
            }, 50);
        }

        scrollToBottom();

        // --- Strategy Selection ---
        if (chatConfig.driver === 'supabase') {
            initSupabase();
        } else if (chatConfig.driver === 'appwrite') {
            initAppwrite();
        } else if (chatConfig.broadcasting && chatConfig.broadcasting.enabled) {
            initEcho();
        } else {
            // Default to Polling (SQLite/Eloquent)
            setInterval(fetchMessages, 3000);
        }

        // --- Supabase Strategy ---
        function initSupabase() {
            const interval = setInterval(() => {
                if (window.supabase) {
                    clearInterval(interval);
                    const { createClient } = window.supabase;
                    const client = createClient(chatConfig.url, chatConfig.key);

                    console.log('Supabase Realtime Initialized');

                    client
                        .channel('public:' + chatConfig.table)
                        .on('postgres_changes', { event: 'INSERT', schema: 'public', table: chatConfig.table }, payload => {
                            const msg = payload.new;
                            // filters
                            if (String(msg.conversation_id) === String(conversationId) && !knownIds.has(msg.id)) {
                                knownIds.add(msg.id);
                                appendMessage(msg);
                                scrollToBottom();
                            }
                        })
                        .subscribe();
                }
            }, 100);
        }

        // --- Appwrite Strategy ---
        function initAppwrite() {
            const interval = setInterval(() => {
                if (window.Appwrite) {
                    clearInterval(interval);
                    const { Client } = window.Appwrite;
                    const client = new Client();

                    client
                        .setEndpoint(chatConfig.endpoint)
                        .setProject(chatConfig.project_id);

                    console.log('Appwrite Realtime Initialized');

                    // Assuming 'messages' collection. 
                    // We subscribe to the documents channel of the messages collection.
                    // Pattern: databases.[ID].collections.[ID].documents
                    const channel = `databases.${chatConfig.database_id}.collections.messages.documents`;

                    client.subscribe(channel, response => {
                        if (response.events.includes('databases.*.collections.*.documents.*.create')) {
                            const msg = response.payload;
                            if (String(msg.conversation_id) === String(conversationId) && !knownIds.has(msg.$id)) {
                                // Adapt Appwrite ID to our format
                                msg.id = msg.$id;
                                knownIds.add(msg.id);
                                appendMessage(msg);
                                scrollToBottom();
                            }
                        }
                    });
                }
            }, 100);
        }

        // --- Laravel Echo (WebSocket) Strategy ---
        function initEcho() {
            if (typeof window.Echo === 'undefined') {
                console.warn('[SimpleChat] Laravel Echo is not defined. Falling back to polling.');
                setInterval(fetchMessages, 3000);
                return;
            }

            window.Echo.private(chatConfig.broadcasting.channel)
                .listen('.MessageSent', (msg) => {
                    if (!knownIds.has(msg.id) && !knownIds.has(String(msg.id))) {
                        knownIds.add(msg.id);
                        appendMessage(msg);
                        scrollToBottom();
                    }
                });
        }

        // --- Shared Logic ---

        async function sendMessage(e) {
            e.preventDefault();
            const content = input.value;
            if (!content.trim()) return;

            input.value = ''; // Optimistic clear
            if (quill) quill.root.innerHTML = '';

            try {
                const response = await fetch(storeUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ content })
                });

                if (response.ok) {
                    // If polling, fetch immediately. 
                    // If realtime, wait for event (or push optimistically if desired).
                    // For simplicity, we can just fetchMessages once to be sure.
                    if (chatConfig.driver !== 'supabase' && chatConfig.driver !== 'appwrite') {
                        fetchMessages();
                    }
                } else {
                    alert(translations.failedToSend);
                }
            } catch (error) {
                console.error('Error:', error);
                alert(translations.errorSending);
            }
        }

        // Legacy Polling Function
        async function fetchMessages() {
            try {
                const response = await fetch(fetchUrl);
                const messages = await response.json();

                let hasNew = false;
                messages.forEach(msg => {
                    // Handle potentially string vs int IDs
                    if (!knownIds.has(msg.id) && !knownIds.has(String(msg.id))) {
                        appendMessage(msg);
                        knownIds.add(msg.id);
                        hasNew = true;
                    }
                });

                if (hasNew) scrollToBottom();

            } catch (error) {
                console.error('Polling error:', error);
            }
        }

        function playNotificationSound() {
            if (chatConfig.sound && chatConfig.sound.enabled && chatConfig.sound.url) {
                const audio = new Audio(chatConfig.sound.url);
                audio.play().catch(e => console.log('Sound play blocked by browser. User interaction might be required first.', e));
            }
        }

        function appendMessage(msg) {
            // Normalize properties
            const senderId = msg.sender_id;
            const isMe = String(senderId) === String(currentUserId);

            // Play sound if message is not from me
            if (!isMe) {
                const playMode = chatConfig.sound ? chatConfig.sound.play_mode : 'inactive';
                if (playMode === 'always' || (document.hidden || !document.hasFocus())) {
                    playNotificationSound();
                }
            }

            const date = new Date(msg.created_at || new Date());
            const msgDateStr = date.toISOString().split('T')[0];

            if (msgDateStr !== lastAppendedDate) {
                const day = date.toDateString() === new Date().toDateString() ? translations.today :
                    (date.toDateString() === new Date(Date.now() - 86400000).toDateString() ? translations.yesterday :
                        date.toLocaleDateString([], { month: 'short', day: 'numeric', year: 'numeric' }));

                const dateDiv = document.createElement('div');
                dateDiv.className = "flex justify-center my-4 sticky top-0 z-10 date-indicator";
                dateDiv.dataset.date = msgDateStr;
                dateDiv.innerHTML = `<span class="px-3 py-1 bg-gray-100/80 backdrop-blur-sm text-gray-500 text-xs rounded-full shadow-sm border border-gray-200">${day}</span>`;
                container.appendChild(dateDiv);
                lastAppendedDate = msgDateStr;
            }

            const template = document.getElementById(isMe ? 'sc-template-me' : 'sc-template-other');
            const clone = template.content.cloneNode(true);
            const msgItem = clone.querySelector('.message-item');
            msgItem.dataset.id = msg.id;

            const time = date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
            clone.querySelector('.sc-msg-time').textContent = time;

            const contentEl = clone.querySelector('.sc-msg-content');
            if (chatConfig.editor === 'wysiwyg') {
                contentEl.innerHTML = msg.content;
                contentEl.classList.add('prose', 'prose-sm', 'max-w-none');
                if (isMe) contentEl.classList.add('text-white');
            } else {
                const p = document.createElement('p');
                p.textContent = msg.content;
                contentEl.appendChild(p);
            }

            const emptyState = document.getElementById('sc-empty-state');
            if (emptyState) emptyState.remove();

            container.appendChild(clone);
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.innerText = text;
            return div.innerHTML;
        }
    </script>
@endsection