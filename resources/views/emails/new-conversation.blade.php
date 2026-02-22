<!DOCTYPE html>
<html>

<head>
    <title>New Conversation Activity</title>
</head>

<body
    style="font-family: Arial, sans-serif; color: #333; line-height: 1.6; max-width: 600px; margin: 0 auto; padding: 20px;">
    <h2>New Conversation Activity</h2>
    <p>A new conversation (ID: {{ $conversationId }}) has been opened or received its first message.</p>

    @if($messageContent)
        <p><strong>First Message:</strong></p>
        <blockquote
            style="border-left: 4px solid #4f46e5; background: #f9fafb; padding: 10px 15px; margin: 20px 0; color: #555;">
            {!! nl2br(e($messageContent)) !!}
        </blockquote>
    @endif

    <p style="margin-top: 30px;">
        <a href="{{ route('simple-chat.show', $conversationId) }}"
            style="background-color: #4f46e5; color: #fff; padding: 10px 20px; text-decoration: none; border-radius: 5px;">
            View Conversation
        </a>
    </p>
</body>

</html>