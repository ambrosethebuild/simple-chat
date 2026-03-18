<?php

namespace SimpleChat\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use SimpleChat\Facades\SimpleChat;
use SimpleChat\Events\ConversationStarted;
use SimpleChat\Events\MessageSent;
use SimpleChat\Jobs\SendAgentAssignedNotificationJob;
use SimpleChat\Jobs\SendNewConversationNotificationJob;
use SimpleChat\Models\Conversation;

class SimpleChatController extends Controller
{
    public function index()
    {
        $userId = auth()->id();
        $mode = config('simple-chat.mode', 'direct');
        $canViewTickets = false;
        $canAssignTickets = false;

        if ($mode === 'support' && auth()->check()) {
            $user = auth()->user();
            $canViewTickets = $user->can(config('simple-chat.support.permissions.view_tickets', 'view-tickets'));
            $canAssignTickets = $user->can(config('simple-chat.support.permissions.assign_tickets', 'assign-tickets'));
        }

        if ($mode === 'support' && $canViewTickets) {
            $conversations = Conversation::latest('updated_at')->get();
        } else {
            $conversations = Conversation::whereJsonContains('participants', (string) $userId)
                ->orWhereJsonContains('participants', (int) $userId) // Handle both string/int IDs
                ->latest('updated_at')
                ->get();
        }

        return view('simple-chat::index', compact('conversations', 'mode', 'canViewTickets', 'canAssignTickets'));
    }

    public function create()
    {
        return view('simple-chat::create');
    }

    public function start(Request $request)
    {
        $authId = auth()->id();
        $mode = config('simple-chat.mode', 'direct');

        if ($mode === 'support') {
            $maxTickets = config('simple-chat.support.max_active_tickets', 3);

            // For simplicity, count conversations where the user is a participant.
            // If the app scales, consider a status column for explicit active/closed distinction.
            $activeCount = Conversation::whereJsonContains('participants', (string) $authId)->where('status', '!=', 'closed')->count();

            if ($activeCount >= $maxTickets) {
                return back()->with('error', "You have reached the maximum number of active tickets ($maxTickets).");
            }

            // Unassigned support chat (only creator in participants initially)
            $participants = [(string) $authId];
            $conversationId = (string) Str::uuid();
        } else {
            $request->validate([
                'participant_id' => 'required',
            ]);

            $participantId = $request->participant_id;

            // Generate a deterministic ID based on participants so they share the same chat room
            $participants = [(string) $authId, (string) $participantId];
            sort($participants);
            $conversationId = md5(implode('_', $participants));
        }

        // The driver will create the conversation if it doesn't exist
        $conversation = SimpleChat::getConversation($conversationId, $participants);

        if (config('simple-chat.broadcasting.enabled', false) && $conversation->wasRecentlyCreated) {
            event(new ConversationStarted($conversation, $mode));
        }

        return redirect()->route('simple-chat.show', $conversationId);
    }

    public function assign($id)
    {
        $mode = config('simple-chat.mode', 'direct');
        if ($mode === 'support') {
            $assignPerm = config('simple-chat.support.permissions.assign_tickets', 'assign-tickets');
            if (auth()->check() && !auth()->user()->can($assignPerm)) {
                abort(403, 'Unauthorized action.');
            }
        }

        $conversation = Conversation::findOrFail($id);

        $participants = is_array($conversation->participants)
            ? $conversation->participants
            : json_decode($conversation->participants, true) ?? [];

        $userId = (string) auth()->id();

        if (!in_array($userId, $participants)) {
            $participants[] = $userId;
            $conversation->update(['participants' => $participants]);
            SendAgentAssignedNotificationJob::dispatch($id, $userId);
        }

        return back()->with('success', 'Assigned successfully.');
    }

    public function close($id)
    {
        $mode = config('simple-chat.mode', 'direct');
        if ($mode === 'support' && auth()->check()) {
            $closePerm = config('simple-chat.support.permissions.close_ticket', 'close-ticket');
            if (!auth()->user()->can($closePerm)) {
                abort(403, 'Unauthorized action.');
            }
        }

        $conversation = Conversation::findOrFail($id);

        // Mark conversation as closed.
        $conversation->update(['status' => 'closed']);

        return redirect()->route('simple-chat.index')->with('success', 'Ticket closed successfully.');
    }

    public function trashed()
    {
        $mode = config('simple-chat.mode', 'direct');
        $deleteMode = config('simple-chat.support.delete_mode', 'soft');

        if ($mode !== 'support' || !auth()->check() || $deleteMode !== 'soft') {
            abort(403, 'Unauthorized action.');
        }

        $viewDeletedPerm = config('simple-chat.support.permissions.view_deleted_tickets', 'view-deleted-tickets');
        if (!auth()->user()->can($viewDeletedPerm)) {
            abort(403, 'Unauthorized action.');
        }

        $conversations = Conversation::onlyTrashed()->latest('deleted_at')->get();

        $user = auth()->user();
        $canViewTickets = $user->can(config('simple-chat.support.permissions.view_tickets', 'view-tickets'));
        $canAssignTickets = $user->can(config('simple-chat.support.permissions.assign_tickets', 'assign-tickets'));

        return view('simple-chat::index', compact('conversations', 'mode', 'canViewTickets', 'canAssignTickets'))->with('showingTrashed', true);
    }

    public function destroy($id)
    {
        $mode = config('simple-chat.mode', 'direct');

        if ($mode === 'support' && auth()->check()) {
            $deletePerm = config('simple-chat.support.permissions.delete_ticket', 'delete-ticket');
            if (!auth()->user()->can($deletePerm)) {
                abort(403, 'Unauthorized action.');
            }
        }

        $conversation = Conversation::withTrashed()->findOrFail($id);

        $deleteMode = config('simple-chat.support.delete_mode', 'soft');
        $routeName = 'simple-chat.index';
        if ($deleteMode === 'hard' || $conversation->trashed()) {
            $conversation->forceDelete();
            $routeName = 'simple-chat.trashed';
        } else {
            $conversation->delete();
        }

        return redirect()->route($routeName)->with('success', 'Ticket deleted successfully.');
    }

    public function restore($id)
    {
        $mode = config('simple-chat.mode', 'direct');
        $deleteMode = config('simple-chat.support.delete_mode', 'soft');

        if ($mode !== 'support' || !auth()->check() || $deleteMode !== 'soft') {
            abort(403, 'Unauthorized action.');
        }

        $deletePerm = config('simple-chat.support.permissions.delete_ticket', 'delete-ticket');
        if (!auth()->user()->can($deletePerm)) {
            abort(403, 'Unauthorized action.');
        }

        $conversation = Conversation::onlyTrashed()->findOrFail($id);
        $conversation->restore();

        return back()->with('success', 'Ticket restored successfully.');
    }

    public function show($id)
    {
        $conversation = Conversation::withTrashed()->findOrFail($id);
        $messages = SimpleChat::getMessages($id, 50)->reverse(); // Show oldest first

        $defaultDriver = config('simple-chat.default');
        $driverConfig = config("simple-chat.drivers.{$defaultDriver}");

        // Filter public config for frontend
        $chatConfig = [
            'driver' => $defaultDriver,
        ];

        if ($defaultDriver === 'appwrite') {
            $chatConfig['endpoint'] = $driverConfig['endpoint'];
            $chatConfig['project_id'] = $driverConfig['project_id'];
            $chatConfig['database_id'] = $driverConfig['database_id'];
        } elseif ($defaultDriver === 'supabase') {
            $chatConfig['url'] = $driverConfig['url'];
            $chatConfig['key'] = $driverConfig['key'];
            $chatConfig['table'] = $driverConfig['table_messages'] ?? 'messages';
        }

        $soundUrl = config('simple-chat.notifications.sound.url');
        if (config('simple-chat.notifications.sound.url_type') === 'local') {
            $soundUrl = asset($soundUrl);
        }

        $chatConfig['sound'] = [
            'enabled' => config('simple-chat.notifications.sound.enabled', true),
            'url' => $soundUrl,
            'play_mode' => config('simple-chat.notifications.sound.play_mode', 'inactive'),
        ];

        $chatConfig['theme'] = config('simple-chat.theme');
        $chatConfig['editor'] = config('simple-chat.editor', 'textarea');
        $chatConfig['broadcasting'] = [
            'enabled' => config('simple-chat.broadcasting.enabled', false),
            'channel' => 'simple-chat.' . $id,
        ];

        $mode = config('simple-chat.mode', 'direct');
        $canReply = true;
        $editor = $chatConfig['editor'];

        if ($mode === 'support' && auth()->check()) {
            $user = auth()->user();
            $participants = is_array($conversation->participants)
                ? $conversation->participants
                : json_decode($conversation->participants, true) ?? [];

            // Typical implementation: creator is the first participant
            $isCreator = isset($participants[0]) && $participants[0] == $user->id;

            if (!$isCreator) {
                $isAssigned = in_array((string) $user->id, $participants) || in_array($user->id, $participants);
                if ($isAssigned) {
                    $canReply = $user->can(config('simple-chat.support.permissions.reply_ticket', 'reply-ticket'));
                } else {
                    $canReply = false;
                }
            }
        }

        if ($conversation->status === 'closed' || $conversation->trashed()) {
            $canReply = false;
        }

        return view('simple-chat::show', compact('conversation', 'messages', 'chatConfig', 'mode', 'canReply'));
    }

    public function fetchMessages($id)
    {
        $messages = SimpleChat::getMessages($id, 50)->reverse()->values();
        return response()->json($messages);
    }

    public function store(Request $request, $id)
    {
        $mode = config('simple-chat.mode', 'direct');
        if ($mode === 'support' && auth()->check()) {
            $conversation = Conversation::findOrFail($id);
            $user = auth()->user();
            $participants = is_array($conversation->participants)
                ? $conversation->participants
                : json_decode($conversation->participants, true) ?? [];

            $isCreator = isset($participants[0]) && $participants[0] == $user->id;

            if (!$isCreator) {
                $isAssigned = in_array((string) $user->id, $participants) || in_array($user->id, $participants);
                if (!$isAssigned) {
                    abort(403, 'You must be assigned to this ticket to reply.');
                }

                $replyPerm = config('simple-chat.support.permissions.reply_ticket', 'reply-ticket');
                if (!$user->can($replyPerm)) {
                    abort(403, 'Unauthorized to reply.');
                }
            }
        }

        $conversation = Conversation::withTrashed()->findOrFail($id);
        if ($conversation->status === 'closed' || $conversation->trashed()) {
            abort(403, 'You cannot reply to a closed or trashed ticket.');
        }

        $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        $rateLimit = config('simple-chat.rate_limit', 60);
        $key = 'send-message:' . auth()->id();

        if (RateLimiter::tooManyAttempts($key, $rateLimit)) {
            abort(429, 'Too many messages sent. Please try again later.');
        }

        RateLimiter::hit($key, 60);

        SimpleChat::sendMessage(
            $id,
            auth()->id(),
            $request->content
        );

        $messages = SimpleChat::getMessages($id, 2);

        if ($messages->count() === 1) {
            SendNewConversationNotificationJob::dispatch($id, $request->content);
        }

        // Send notification for EACH message if enabled
        if (config('simple-chat.notifications.each_message.enabled')) {
            \SimpleChat\Jobs\SendNewMessageNotificationJob::dispatch($id, auth()->id(), $request->content);
        }

        // Broadcast via WebSocket if enabled
        if (config('simple-chat.broadcasting.enabled', false)) {
            $latest = $messages->first();
            if ($latest) {
                event(new MessageSent($conversation->id, [
                    'id'              => $latest->id,
                    'conversation_id' => $conversation->id,
                    'sender_id'       => $latest->sender_id,
                    'content'         => $latest->content,
                    'created_at'      => (string) $latest->created_at,
                ]));
            }
        }

        return back();
    }
}
