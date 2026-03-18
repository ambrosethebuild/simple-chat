@extends(config('simple-chat.layout', 'layouts.app'))

@section('title', config('simple-chat.titles.create', __('simple-chat::messages.titles.create')))
@section(config('simple-chat.section', 'content'))
    <div class="max-w-xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
        <div class="mb-8">
            <a href="{{ route('simple-chat.index') }}"
                class="text-sm font-medium {{ config('simple-chat.theme.primary_text', 'text-indigo-600') }} hover:opacity-80">&larr;
                {{ __('simple-chat::messages.actions.back_to_messages') }}</a>
        </div>

        @if(session('error'))
            <div class="rounded-md bg-red-50 p-4 mb-8 border border-red-200">
                <div class="flex">
                    <div class="ml-3 text-sm text-red-700">
                        {{ session('error') }}
                    </div>
                </div>
            </div>
        @endif

        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    {!! config('simple-chat.titles.create', __('simple-chat::messages.titles.create')) !!}
                </h3>
                @if(config('simple-chat.mode') === 'support')
                    <p class="mt-1 max-w-2xl text-sm text-gray-500">
                        {{ __('simple-chat::messages.status.support_start_desc') }}
                    </p>
                @else
                    <p class="mt-1 max-w-2xl text-sm text-gray-500">
                        {{ __('simple-chat::messages.status.direct_start_desc') }}
                    </p>
                @endif
            </div>
            <div class="border-t border-gray-200 px-4 py-5 sm:p-0">
                <form action="{{ route('simple-chat.start') }}" method="POST" class="sm:p-6 p-4">
                    @csrf
                    @if(config('simple-chat.mode') !== 'support')
                        <div>
                            <label for="participant_id" class="block text-sm font-medium text-gray-700">{{ __('simple-chat::messages.labels.user_id') }}</label>
                            <div class="mt-1">
                                <input type="text" name="participant_id" id="participant_id"
                                    class="shadow-sm {{ config('simple-chat.theme.primary_ring', 'focus:ring-indigo-500') }} {{ config('simple-chat.theme.primary_border', 'focus:border-indigo-500') }} block w-full sm:text-sm border-gray-300 rounded-md p-2 border"
                                    placeholder="e.g. 5" required>
                            </div>
                        </div>
                    @endif

                    <div class="mt-5 sm:mt-6">
                        <button type="submit"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 {{ config('simple-chat.theme.primary_color', 'bg-indigo-600') }} text-base font-medium text-white {{ config('simple-chat.theme.primary_hover', 'hover:bg-indigo-700') }} focus:outline-none focus:ring-2 focus:ring-offset-2 {{ config('simple-chat.theme.primary_ring', 'focus:ring-indigo-500') }} sm:text-sm transition-colors duration-200">
                            {{ __('simple-chat::messages.actions.start_chatting') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection