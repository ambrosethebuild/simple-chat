<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('simple-chat::messages.emails.agent_assigned.title') }}</title>
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

        .icon-wrap {
            text-align: center;
            margin-bottom: 24px;
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
                                <h1>{{ __('simple-chat::messages.emails.agent_assigned.title') }}</h1>
                            </td>
                        </tr>
                        <tr>
                            <td class="content">
                                <div class="icon-wrap">
                                    <svg style="width: 64px; height: 64px; color: #4f46e5; display: inline-block;"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z">
                                        </path>
                                    </svg>
                                </div>
                                <p style="text-align: center; font-size: 18px; color: #111827;">{{ __('simple-chat::messages.emails.agent_assigned.body') }}</p>
                                <p style="text-align: center; margin-top: 8px;">{{ __('simple-chat::messages.emails.agent_assigned.sub_body') }}</p>

                                <div class="button-wrap">
                                    <a href="{{ route('simple-chat.show', $conversationId) }}" class="button">{{ __('simple-chat::messages.emails.agent_assigned.button') }}</a>
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