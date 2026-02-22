# Simple Chat

A flexible, drop-in Laravel chat package designed to support both peer-to-peer messaging and customer support ticket flows. It features multiple adaptable backends, including self-hosted SQL databases and seamless frontend realtime integrations using Supabase or Appwrite.

## Features

- **Multi-Driver Architecture:** Swap between `sqlite_sharded`, `eloquent` (MySQL/PostgreSQL), `appwrite`, or `supabase` seamlessly.
- **Two Distinct Modes:** Run peer-to-peer direct conversations (`direct`) or assignment-based helpdesk ticket flows (`support`).
- **Support Workflows:** Assign agents, enforce Role-Based Access Control (RBAC) permissions (view, assign, reply, close), and prevent duplicated active tickets per user.
- **Realtime Ready:** Features built-in out-of-the-box frontend listener strategy for Appwrite and Supabase Realtime SDKs, seamlessly falling back to long-polling for standard drivers.
- **Ticket Lifecycles:** Close dormant support tickets completely using a clean ticket-closing mechanic.
- **Rich User Interface:** Uses Tailwind CSS, bundled with elegant, configurable themes. Use a clean auto-expanding textarea or toggle the robust WYSIWYG editor (Quill.js). 
- **Isolated Blade Components:** Clean customizable views mapped through configuration values.

---

## Installation

You can install the package via composer:

```bash
composer require ambrosethebuild/simple-chat
```

Publish the package configuration file, migrations, and views:

```bash
php artisan vendor:publish --provider="SimpleChat\SimpleChatServiceProvider" --tag="config"
php artisan vendor:publish --provider="SimpleChat\SimpleChatServiceProvider" --tag="views"
```

Then run your migrations:

```bash
php artisan migrate
```

> **Note on Styling:** Since the views utilize Tailwind CSS, make sure to add the package's view paths to your `tailwind.config.js` `content` array so its utility classes are compiled properly:
> ```js
> content: [
>     // ...
>     "./vendor/vendor/simple-chat/resources/views/**/*.blade.php",
> ],
> ```

---

## Configuration

The published `config/simple-chat.php` gives you granular control over features, modes, themes, and text parameters. Here are the core environmental toggles:

### 1. Modes

Set the chat workflow mode directly using your `.env` file:
```dotenv
# "direct" or "support"
SIMPLE_CHAT_MODE=support
```

### 2. Supported Drivers

Out of the box, Simple Chat supports four powerful persistent layers:

```dotenv
# "sqlite_sharded", "eloquent", "appwrite", "supabase"
CHAT_DRIVER=eloquent
```

**Supabase Integration Example:**
```dotenv
SUPABASE_URL="https://your-project.supabase.co"
SUPABASE_KEY="your-anon-key"
```

**Appwrite Integration Example:**
```dotenv
APPWRITE_ENDPOINT="https://cloud.appwrite.io/v1"
APPWRITE_PROJECT_ID="your-project-id"
APPWRITE_DATABASE_ID="your-database-id"
```

### 3. Editor UI configuration

By default, users compose messages with a clean, native auto-expanding textarea. If you want rich text formatting (bold, italic, bullets, links) powered natively by Quill.js:
    
```dotenv
# "textarea" or "wysiwyg"
SIMPLE_CHAT_EDITOR=wysiwyg
```

### 4. Support Permissions & Restrictions

If you're using `SIMPLE_CHAT_MODE=support`, Laravel's authorization handles fine-grained actions. Update these in your `.env` or implement their defaults via your auth gating mechanics:

```dotenv
SIMPLE_CHAT_PERM_VIEW=view-tickets
SIMPLE_CHAT_PERM_ASSIGN=assign-tickets
SIMPLE_CHAT_PERM_REPLY=reply-ticket
SIMPLE_CHAT_PERM_CLOSE=close-ticket
SIMPLE_CHAT_MAX_TICKETS=3
```

---

## Overriding Views and Customization

By publishing the internal views, you will find fully exposed responsive Blade views at `resources/views/vendor/simple-chat`.

If you just want to tweak basic themes and copies, use the primary unified `config/simple-chat.php` configuration blocks:
```php
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
```

## Using the Frontend Engine

Head over to your configured application root route, natively set to `yourdomain.test/chat` right out of the box!

```php
// Modifiable prefix inside config/simple-chat.php
'route_prefix' => 'chat', 
```

Enjoy building scalable chat systems swiftly!

## License

[MIT License](LICENSE)
