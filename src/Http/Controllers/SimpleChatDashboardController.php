<?php

namespace SimpleChat\Http\Controllers;

use Illuminate\Routing\Controller;
use SimpleChat\Models\Conversation;

class SimpleChatDashboardController extends Controller
{
    public function index()
    {
        $mode = config('simple-chat.mode', 'direct');

        if ($mode !== 'support' || !auth()->check()) {
            abort(403, 'Unauthorized action.');
        }

        $viewPerm = config('simple-chat.support.permissions.view_tickets', 'view-tickets');
        if (!auth()->user()->can($viewPerm)) {
            abort(403, 'Unauthorized action.');
        }

        $allConversations = Conversation::all();

        $totalTickets      = $allConversations->count();
        $openTickets       = $allConversations->where('status', '!=', 'closed')->count();
        $closedTickets     = $allConversations->where('status', 'closed')->count();
        $unassignedTickets = $allConversations->filter(function ($conv) {
            $parts = is_array($conv->participants)
                ? $conv->participants
                : json_decode($conv->participants ?? '[]', true);
            return count($parts ?? []) <= 1 && $conv->status !== 'closed';
        })->count();

        // KPI row 2
        $todayTickets   = $allConversations->where('created_at', '>=', now()->startOfDay())->count();
        $weekTickets    = $allConversations->where('created_at', '>=', now()->startOfWeek())->count();
        $resolutionRate = $totalTickets > 0 ? round(($closedTickets / $totalTickets) * 100) : 0;
        $deletedTickets = Conversation::onlyTrashed()->count();

        // Tickets opened per day — last 30 days (labels + counts for chart)
        $ticketsPerDay = collect();
        for ($i = 29; $i >= 0; $i--) {
            $ticketsPerDay[now()->subDays($i)->format('Y-m-d')] = 0;
        }
        foreach ($allConversations as $conv) {
            $date = $conv->created_at->format('Y-m-d');
            if (isset($ticketsPerDay[$date])) {
                $ticketsPerDay[$date]++;
            }
        }

        // Recent conversations (last 5)
        $recentConversations = Conversation::latest('updated_at')->take(5)->get();

        // Build per-agent stats (all participants except index-0 creator are agents)
        $agentStats = [];
        foreach ($allConversations as $conv) {
            $parts = is_array($conv->participants)
                ? $conv->participants
                : json_decode($conv->participants ?? '[]', true);

            $agents = array_slice($parts ?? [], 1);

            foreach ($agents as $agentId) {
                $agentId = (string) $agentId;
                if (!isset($agentStats[$agentId])) {
                    $agentStats[$agentId] = ['total' => 0, 'open' => 0, 'closed' => 0, 'last_activity' => null];
                }
                $agentStats[$agentId]['total']++;
                if ($conv->status === 'closed') {
                    $agentStats[$agentId]['closed']++;
                } else {
                    $agentStats[$agentId]['open']++;
                }
                if (!$agentStats[$agentId]['last_activity'] || $conv->updated_at > $agentStats[$agentId]['last_activity']) {
                    $agentStats[$agentId]['last_activity'] = $conv->updated_at;
                }
            }
        }

        $userClass = config('auth.providers.users.model', '\App\Models\User');
        $agentIds  = array_keys($agentStats);
        $agents    = $agentIds ? $userClass::whereIn('id', $agentIds)->get()->keyBy('id') : collect();

        return view('simple-chat::dashboard', compact(
            'totalTickets', 'openTickets', 'closedTickets', 'unassignedTickets',
            'todayTickets', 'weekTickets', 'resolutionRate', 'deletedTickets',
            'ticketsPerDay', 'recentConversations',
            'agentStats', 'agents'
        ));
    }
}
