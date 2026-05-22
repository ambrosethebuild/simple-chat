@props(['message'])

<div class="flex {{ $message->sender_id == auth()->id() ? 'justify-end' : 'justify-start' }} message-item"
    data-id="{{ $message->id }}">
    <div
        class="max-w-xs lg:max-w-md px-4 py-2 rounded-lg shadow-sm {{ $message->sender_id == auth()->id() ? config('simple-chat.theme.primary_color', 'bg-indigo-600') . ' text-white rounded-br-none' : 'bg-white text-gray-900 rounded-bl-none' }}">
        @if(config('simple-chat.editor') === 'wysiwyg')
            <div class="text-sm prose prose-sm max-w-none {!! $message->sender_id == auth()->id() ? 'text-white' : '' !!}">
                {!! $message->content !!}</div>
        @else
            <div class="text-sm sc-plain-message">{{ $message->content }}</div>
        @endif
        <p class="text-xs mt-1 {{ $message->sender_id == auth()->id() ? 'text-indigo-200' : 'text-gray-400' }}">
            {{ $message->created_at->format('H:i') }}
        </p>
    </div>
</div>
