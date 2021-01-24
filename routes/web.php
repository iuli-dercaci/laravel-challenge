<?php

use App\Http\Controllers\TicketController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/open-tickets', [TicketController::class, 'openTickets']);
Route::get('/closed-tickets', [TicketController::class, 'closedTickets']);
Route::get('/users/{email}/tickets', [TicketController::class, 'userTickets']);
Route::get('/stats', [TicketController::class, 'stats']);
