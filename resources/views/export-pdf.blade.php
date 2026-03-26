<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Chat Export #{{ $conversation->id }}</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #eee;
            padding-bottom: 10px;
        }
        .message {
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #f9f9f9;
        }
        .message-header {
            font-weight: bold;
            color: #555;
            margin-bottom: 5px;
        }
        .message-time {
            font-size: 10px;
            color: #999;
            float: right;
        }
        .message-content {
            white-space: pre-wrap;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Chat Export</h1>
        <p>Conversation ID: {{ $conversation->id }}</p>
        <p>Exported on: {{ now()->format('F j, Y, g:i a') }}</p>
    </div>

    @foreach($messages as $message)
        <div class="message">
            <div class="message-header">
                {{ $message->sender_name ?? "User #{$message->sender_id}" }}
                <span class="message-time">{{ $message->created_at->format('Y-m-d H:i:s') }}</span>
            </div>
            <div class="message-content">{{ $message->content }}</div>
        </div>
    @endforeach
</body>
</html>
