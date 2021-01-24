<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function openTickets(): void
    {
        abort(200);
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
