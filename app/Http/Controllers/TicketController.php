<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function openTickets(Request $request)
    {
        $perPage = $request->get('per_page') ?: 10;
        return Ticket::open()->paginate($perPage);
    }

    public function closedTickets(): void
    {
        abort(200);
    }

    public function userTickets(): void
    {
        abort(200);
    }

    public function stats(): void
    {
        abort(200);
    }
}
