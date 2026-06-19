<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Ticket;

class TicketController extends Controller
{
    public function index()
    {
        $member = auth('member')->user();

        $tickets = Ticket::where('member_id', $member->id)
            ->with('event.venue')
            ->latest()
            ->get();

        return view('panel.tickets.index', compact('member', 'tickets'));
    }

    public function show(Ticket $ticket)
    {
        $member = auth('member')->user();

        if ((int) $ticket->member_id !== (int) $member->id) {
            abort(403);
        }

        $ticket->load('event.venue', 'member');

        return view('panel.tickets.show', compact('ticket'));
    }
}
