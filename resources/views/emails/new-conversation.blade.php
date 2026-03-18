<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('simple-chat::messages.emails.new_conversation.title') }}</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            min-width: 100%;
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background-color: #f3f4f6;
            color: #374151;
        }

        .wrapper {
            width: 100%;
            table-layout: fixed;
            background-color: #f3f4f6;
            padding-bottom: 40px;
        }

        .main {
            background-color: #ffffff;
            margin: 0 auto;
            width: 100%;
            max-width: 600px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .header {
            background-color: #4f46e5;
            padding: 24px;
            text-align: center;
        }

        .header h1 {
            margin: 0;
            color: #ffffff;
            font-size: 24px;
            font-weight: 600;
        }

        .content {
            padding: 32px;
        }

        .content p {
            margin: 0 0 16px 0;
            font-size: 16px;
            line-height: 1.5;
            color: #4b5563;
        }

        .message-box {
            background-color: #f9fafb;
            border-left: 4px solid #4f46e5;
            padding: 16px;
            margin: 24px 0;
            border-radius: 0 4px 4px 0;
        }

        .message-box p {
            margin: 0;
            color: #1f2937;
        }

        .button-wrap {
            text-align: center;
            margin-top: 32px;
            margin-bottom: 16px;
        }

        .button {
            display: inline-block;
            background-color: #4f46e5;
            color: #ffffff !important;
            text-decoration: none;
            padding: 12px 24px;
            border-radius: 6px;
            font-weight: 500;
            font-size: 16px;
        }

        .footer {
            text-align: center;
            padding: 24px;
            color: #9ca3af;
            font-size: 14px;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <table width="100%" cellpadding="0" cellspacing="0" border="0">
            <tr>
                <td align="center" style="padding-top: 40px; padding-bottom: 40px;">
                    <table class="main" cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <td class="header">
                                <h1>{{ __('simple-chat::messages.emails.new_conversation.title') }}</h1>
                            </td>
                        </tr>
                        <tr>
                            <td class="content">
                                <p>{!! __('simple-chat::messages.emails.new_conversation.body', ['id' => '<strong>' . $conversationId . '</strong>']) !!}</p>

                                @if($messageContent)
                                    <div class="message-box">
                                        <p style="font-size: 14px; color: #6b7280; margin-bottom: 8px;"><strong>{{ __('simple-chat::messages.labels.first_message') }}</strong></p>
                                        <p>{!! nl2br(e($messageContent)) !!}</p>
                                    </div>
                                @endif

                                <div class="button-wrap">
                                    <a href="{{ route('simple-chat.show', $conversationId) }}" class="button">{{ __('simple-chat::messages.emails.new_conversation.button') }}</a>
                                </div>
                            </td>
                        </tr>
                    </table>
                    <table width="100%" cellpadding="0" cellspacing="0" border="0"
                        style="max-width: 600px; margin: 0 auto;">
                        <tr>
                            <td class="footer">
                                <p style="margin: 0;">&copy; {{ __('simple-chat::messages.emails.footer', ['year' => date('Y')]) }}</p>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>
</body>

</html>