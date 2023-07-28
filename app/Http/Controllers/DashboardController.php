<?php

namespace App\Http\Controllers;

use App\Models\Timesheet;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {

        $t = new Timesheet();

        $minutes = $t->where('user_id', '=', Auth::user()->id)->thisYear()->sum('totalt');

        $timerJobbet = sprintf('%02d', intdiv($minutes, 60)).'t '.(sprintf('%02d', $minutes % 60).'m');

        $nesteArbeidstid = $t->whereDate('fra_dato', '>', today())->get();

        return view('dashboard', compact('timerJobbet', 'nesteArbeidstid'));
    }
}
