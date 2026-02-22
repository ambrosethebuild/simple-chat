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
            'view_tickets' => env('SIMPLE_CHAT_PERM_VIEW', 'view-tickets'),
            'assign_tickets' => env('SIMPLE_CHAT_PERM_ASSIGN', 'assign-tickets'),
            'reply_ticket' => env('SIMPLE_CHAT_PERM_REPLY', 'reply-ticket'),
            'close_ticket' => env('SIMPLE_CHAT_PERM_CLOSE', 'close-ticket'),
        ],

        // Maximum number of active tickets a user can have open
        'max_active_tickets' => env('SIMPLE_CHAT_MAX_TICKETS', 3),
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
