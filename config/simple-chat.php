<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Chat Driver
    |--------------------------------------------------------------------------
    |
    | This option controls the default chat driver that will be used by the
    | package. You may set this to any of the connections defined in the
    | "drivers" array below.
    |
    | Supported: "sqlite_sharded", "eloquent", "appwrite"
    |
    */
    'default' => env('CHAT_DRIVER', 'sqlite_sharded'),

    /*
    |--------------------------------------------------------------------------
    | Application Mode
    |--------------------------------------------------------------------------
    |
    | Supported modes:
    | 'direct'  : Peer-to-peer chats. Requires a participant ID when creating.
    | 'support' : Ticket-like flow. Users create unassigned chats. Support agents
    |             can view and assign themselves.
    |
    */
    'mode' => env('SIMPLE_CHAT_MODE', 'direct'),

    /*
    |--------------------------------------------------------------------------
    | Support Configuration
    |--------------------------------------------------------------------------
    |
    | Only utilized if 'mode' is set to 'support'.
    |
    */
    'support' => [
        // Permissions required for support agents. 
        // These will be checked using Laravel's `$user->can(...)` authorization.
        'permissions' => [
            'view_tickets'        => env('SIMPLE_CHAT_PERM_VIEW',         'view-tickets'),
            'assign_tickets'      => env('SIMPLE_CHAT_PERM_ASSIGN',        'assign-tickets'),
            'unassign_agent'      => env('SIMPLE_CHAT_PERM_UNASSIGN',      'unassign-agent'),
            'reply_ticket'        => env('SIMPLE_CHAT_PERM_REPLY',         'reply-ticket'),
            'close_ticket'        => env('SIMPLE_CHAT_PERM_CLOSE',         'close-ticket'),
            'delete_ticket'       => env('SIMPLE_CHAT_PERM_DELETE',        'delete-ticket'),
            'view_deleted_tickets'=> env('SIMPLE_CHAT_PERM_VIEW_DELETED',  'view-deleted-tickets'),

            // A super-admin holder bypasses all other permission checks and can
            // oversee all conversations, access the dashboard, unassign agents, etc.
            'super_admin'         => env('SIMPLE_CHAT_PERM_SUPER_ADMIN',   'simple-chat-super-admin'),
        ],

        // Maximum number of active tickets a user can have open
        'max_active_tickets' => env('SIMPLE_CHAT_MAX_TICKETS', 3),

        // How should tickets be deleted? 'soft' or 'hard'
        'delete_mode' => env('SIMPLE_CHAT_DELETE_MODE', 'soft'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Route Configuration
    |--------------------------------------------------------------------------
    |
    | Configure the route prefix and middleware for the chat interface.
    |
    */
    'route_prefix' => 'chat',

    'middleware' => ['web', 'auth'],

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    |
    | Define how many messages a single user can send per minute to prevent DOS
    | attacks. Default is 60 messages per minute.
    |
    */
    'rate_limit' => env('SIMPLE_CHAT_RATE_LIMIT', 60),

    /*
    |--------------------------------------------------------------------------
    | Notifications
    |--------------------------------------------------------------------------
    |
    | Provide a comma-separated list of email addresses that should be alerted
    | when a new ticket is opened. Leave blank to disable alerts entirely.
    |
    */
    'notifications' => [
        'email_addresses' => env('SIMPLE_CHAT_NOTIFY_EMAILS', ''),
        'each_message' => [
            'enabled' => env('SIMPLE_CHAT_NOTIFY_EACH_MESSAGE', false),
            // if from user, send to support team (config('simple-chat.notifications.email_addresses'))
            // if from support team, send to client (conversation creator)
        ],

        // Minimum number of minutes that must pass before another notification
        // email is sent for the same conversation. Prevents email flooding in
        // very active chats. Set to 0 to disable throttling entirely.
        'email_throttle_minutes' => env('SIMPLE_CHAT_EMAIL_THROTTLE', 5),
        'sound' => [
            'enabled' => env('SIMPLE_CHAT_NOTIFY_SOUND', true),
            'url' => env('SIMPLE_CHAT_NOTIFY_SOUND_URL', 'https://assets.mixkit.co/active_storage/sfx/2354/2354-preview.mp3'),
            'url_type' => env('SIMPLE_CHAT_NOTIFY_SOUND_TYPE', 'url'), // 'url' or 'local'
            'play_mode' => env('SIMPLE_CHAT_NOTIFY_SOUND_MODE', 'inactive'), // 'always' or 'inactive'
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Message Input Editor
    |--------------------------------------------------------------------------
    |
    | Define what type of input editor should be used for the chat.
    |
    | Supported: "textarea" (default auto-expanding), "wysiwyg" (Quill.js)
    |
    */
    'editor' => env('SIMPLE_CHAT_EDITOR', 'textarea'),

    /*
    |--------------------------------------------------------------------------
    | View Configuration
    |--------------------------------------------------------------------------
    |
    | Configure the layout and section name for the chat views, as well as
    | specific design themes (like colors) to match your app interface.
    |
    | Note: These are utilized directly in the package's Blade components.
    | To use custom Tailwind classes here, ensure your app's tailwind.config.js
    | is scanning the package's view directory, or use standard CSS colors.
    |
    */
    'layout' => 'layouts.app',

    'section' => 'content',

    'titles' => [
        'index' => 'Messages',
        'create' => 'Start a Conversation',
        'show' => 'Conversation',
    ],

    'theme' => [
        'primary_color' => 'bg-indigo-600',
        'primary_hover' => 'hover:bg-indigo-700',
        'primary_text' => 'text-indigo-600',
        'primary_ring' => 'focus:ring-indigo-500',
        'primary_border' => 'focus:border-indigo-500',
        'secondary_bg' => 'bg-gray-50',
    ],

    /*
    |--------------------------------------------------------------------------
    | Broadcasting (WebSocket)
    |--------------------------------------------------------------------------
    |
    | When enabled, a `MessageSent` event is dispatched on the private channel
    | `simple-chat.{conversationId}` each time a message is stored. The
    | frontend will use Laravel Echo to receive messages in real time instead
    | of the default HTTP polling strategy.
    |
    | Requires Laravel Echo and a compatible broadcaster (Reverb, Pusher,
    | Soketi, etc.) to be configured in your application.
    |
    | Channel authorization is handled automatically by the package's channel
    | route (only conversation participants may subscribe). You may customize
    | this by publishing the channels file:
    |   php artisan vendor:publish --tag=simple-chat-channels
    |
    */
    'broadcasting' => [
        'enabled' => env('SIMPLE_CHAT_BROADCASTING', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Chat Drivers
    |--------------------------------------------------------------------------
    |
    | Here you may configure the settings for each driver.
    |
    */
    'drivers' => [
        'sqlite_sharded' => [
            'driver' => \SimpleChat\Drivers\SqliteShardedDriver::class,
            'storage_path' => storage_path('app/chats'),
        ],

        'eloquent' => [
            'driver' => \SimpleChat\Drivers\EloquentDriver::class,
            'connection' => env('DB_CONNECTION', 'mysql'),
            'table_messages' => 'simple_chat_messages',
            'table_conversations' => 'simple_chat_conversations',
        ],

        'appwrite' => [
            'driver' => \SimpleChat\Drivers\AppwriteDriver::class,
            'endpoint' => env('APPWRITE_ENDPOINT'),
            'project_id' => env('APPWRITE_PROJECT_ID'),
            'api_key' => env('APPWRITE_API_KEY'),
            'database_id' => env('APPWRITE_DATABASE_ID'),
        ],

        'supabase' => [
            'driver' => \SimpleChat\Drivers\SupabaseDriver::class, // Wrapper around Eloquent with Realtime
            'url' => env('SUPABASE_URL'),
            'key' => env('SUPABASE_KEY'),
            'table_messages' => 'messages',
            'connection' => 'pgsql',
        ],
    ],
];
