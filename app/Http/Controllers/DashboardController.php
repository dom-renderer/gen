<?php

namespace App\Http\Controllers;

use App\Models\Custodian;
use App\Models\InvestmentDedicatedFund;
use App\Models\Policy;
use App\Models\PolicyHolder;
use App\Models\PolicyIntroducer;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        $from = \Carbon\Carbon::now()->startOfMonth()->format('d-m-Y');
        $to = \Carbon\Carbon::now()->endOfMonth()->format('d-m-Y');

        if ($request->filled('date_range')) {
            $date = explode(' - ', $request->date_range);
            if (is_array($date) && count($date) == 2) {
                $from = date('d-m-Y', strtotime($date[0]));
                $to = date('d-m-Y', strtotime($date[1]));
            }
        }

        $fromFormatted = \Carbon\Carbon::createFromFormat('d-m-Y', $from)->format('Y-m-d');
        $toFormatted = \Carbon\Carbon::createFromFormat('d-m-Y', $to)->format('Y-m-d');

        $cases = Policy::where('status', 'ACTIVE')->whereRaw("DATE_FORMAT(created_at, '%Y-%m-%d') >= ? AND DATE_FORMAT(created_at, '%Y-%m-%d') <= ?", [$fromFormatted, $toFormatted])->count();
        $surrender = Policy::where('status', 'SURRENDERED')->whereRaw("DATE_FORMAT(created_at, '%Y-%m-%d') >= ? AND DATE_FORMAT(created_at, '%Y-%m-%d') <= ?", [$fromFormatted, $toFormatted])->count();
        $terminated = Policy::where('status', 'TERMINATED')->whereRaw("DATE_FORMAT(created_at, '%Y-%m-%d') >= ? AND DATE_FORMAT(created_at, '%Y-%m-%d') <= ?", [$fromFormatted, $toFormatted])->count();
        $exchange = Policy::where('status', '1035 EXCHANGE')->whereRaw("DATE_FORMAT(created_at, '%Y-%m-%d') >= ? AND DATE_FORMAT(created_at, '%Y-%m-%d') <= ?", [$fromFormatted, $toFormatted])->count();
        $lapse = Policy::where('status', 'LAPSE')->whereRaw("DATE_FORMAT(created_at, '%Y-%m-%d') >= ? AND DATE_FORMAT(created_at, '%Y-%m-%d') <= ?", [$fromFormatted, $toFormatted])->count();
        $prospect = Policy::where('status', 'PROSPECT')->whereRaw("DATE_FORMAT(created_at, '%Y-%m-%d') >= ? AND DATE_FORMAT(created_at, '%Y-%m-%d') <= ?", [$fromFormatted, $toFormatted])->count();
        $underreview = Policy::where('status', 'UNDER REVIEW')->whereRaw("DATE_FORMAT(created_at, '%Y-%m-%d') >= ? AND DATE_FORMAT(created_at, '%Y-%m-%d') <= ?", [$fromFormatted, $toFormatted])->count();

        $idf = InvestmentDedicatedFund::whereRaw("DATE_FORMAT(created_at, '%Y-%m-%d') >= ? AND DATE_FORMAT(created_at, '%Y-%m-%d') <= ?", [$fromFormatted, $toFormatted])->count();
        $custodians = Custodian::whereRaw("DATE_FORMAT(created_at, '%Y-%m-%d') >= ? AND DATE_FORMAT(created_at, '%Y-%m-%d') <= ?", [$fromFormatted, $toFormatted])->count();
        $introducers = PolicyIntroducer::whereRaw("DATE_FORMAT(created_at, '%Y-%m-%d') >= ? AND DATE_FORMAT(created_at, '%Y-%m-%d') <= ?", [$fromFormatted, $toFormatted])->count();

        return view('home', compact('cases', 'surrender', 'terminated', 'exchange', 'lapse', 'prospect', 'underreview', 'introducers', 'custodians', 'idf'));
    }
}
