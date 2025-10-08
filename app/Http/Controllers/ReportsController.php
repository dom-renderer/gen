<?php

namespace App\Http\Controllers;

use App\Models\Policy;
use App\Models\PolicyIntroducer;
use App\Models\PolicyHolder;
use App\Models\PolicyInsuredLifeInformation;
use App\Models\InvestmentDedicatedFund;
use App\Exports\StatusReportExport;
use App\Exports\NewPoliciesExport;
use App\Models\PolicyDocument;
use App\Models\Document;
use App\Models\PolicyController;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\MissingDocumentExport;
use App\Exports\PoliciesByStatusExport;

class ReportsController extends Controller
{
    public function policiesByStatus(Request $request): View
    {
        $statuses = [
            'active', 'surrendered', 'terminated', 'prospect', '1035 EXCHANGE', 'under review', 'lapse', 'draft'
        ];

        $summary = Policy::selectRaw('LOWER(status) as status_key, status, COUNT(*) as total')
            ->groupBy('status')
            ->get()
            ->keyBy('status_key');

        $filterStatus = $request->query('status');
        $policiesQuery = Policy::query()->orderByDesc('id');
        if ($filterStatus) {
            $policiesQuery->whereRaw('LOWER(status) = ?', [strtolower($filterStatus)]);
        } else {
            $policiesQuery->whereIn('status', $statuses);
        }

        $policies = $policiesQuery->paginate(25)->withQueryString();

        return view('reports.policies_by_status', compact('statuses', 'summary', 'policies', 'filterStatus'));
    }

    public function exportPoliciesByStatus(Request $request)
    {
        $status = $request->query('status');
        $filename = 'policies_by_status' . ($status ? '_' . strtolower($status) : '') . '.xlsx';
        return Excel::download(new PoliciesByStatusExport($status), $filename);
    }

    public function newPolicies(Request $request)
    {
        $now = Carbon::now();
        $startOfYear = $now->copy()->startOfYear();
        $startOfQuarter = $now->copy()->firstOfQuarter();
        $startOfMonth = $now->copy()->startOfMonth();

        if ($request->ajax()) {
            $range = strtolower((string)$request->query('range', 'ytd'));
            $from = $startOfYear;
            if ($range === 'qtd') { $from = $startOfQuarter; }
            if ($range === 'mtd') { $from = $startOfMonth; }

            $policy = Policy::query()
                ->whereNotNull('opening_date')
                ->whereBetween('opening_date', [$from, $now])
                ->orderByDesc('opening_date');

            return datatables()
                ->eloquent($policy)
                ->editColumn('opening_date', fn ($row) => $row->opening_date ? Carbon::parse($row->opening_date)->format('Y-m-d') : null)
                ->addColumn('theholder', function ($row) {
                    $ph = PolicyHolder::where('policy_id', $row->id)->first();
                    return $ph?->name ?: 'N/A';
                })
                ->addColumn('introducer', function ($row) {
                    $pi = PolicyIntroducer::where('policy_id', $row->id)->first();
                    return $pi?->name ?: 'N/A';
                })
                ->addColumn('idfmgr', function ($row) {
                    $idf = InvestmentDedicatedFund::where('policy_id', $row->id)->where('user_type', 'manager')->first();
                    if (!$idf) return 'N/A';
                    if ($idf->type === 'Individual') {
                        return collect([$idf->first_name, $idf->middle_name, $idf->last_name])->filter()->implode(' ');
                    }
                    return $idf->name ?: 'N/A';
                })
                ->editColumn('status', function ($row) {
                    return (string)($row->status ?? '');
                })
                ->addColumn('liklihood_c', function ($row) {
                    $lh = \App\Models\Liklihood::find($row->liklihood);


                    $html = '<div class="capsule-report"> ' . ($lh->name ?? '') . ' </div>';

                    return $html;
                })
                ->addColumn('action', function ($row) {
                    $html = '<ul>';
                    if (auth()->user()->can('cases.view')) {
                        $html .= '<li><a href="' . route('cases.view', encrypt($row->id)) . '"> View </a></li>';
                    }
                    $html .= '</ul>';
                    return $html;
                })
                ->addIndexColumn()
                ->rawColumns(['action', 'liklihood_c'])
                ->toJson();
        }

        $baseQuery = Policy::query()->whereNotNull('opening_date');
        $ytd = (clone $baseQuery)->whereBetween('opening_date', [$startOfYear, $now])->orderByDesc('opening_date')->get();
        $qtd = (clone $baseQuery)->whereBetween('opening_date', [$startOfQuarter, $now])->orderByDesc('opening_date')->get();
        $mtd = (clone $baseQuery)->whereBetween('opening_date', [$startOfMonth, $now])->orderByDesc('opening_date')->get();

        return view('reports.new_policies', compact('ytd', 'qtd', 'mtd', 'startOfYear', 'startOfQuarter', 'startOfMonth', 'now'));
    }

    public function exportNewPolicies(Request $request)
    {
        $range = strtolower((string)$request->query('range', 'ytd'));
        return Excel::download(new NewPoliciesExport($range), 'new_policies_' . $range . '.xlsx');
    }

    public function topIntroducers(Request $request): View
    {
        $limit = (int)($request->query('limit', 10));
        $introducerName = $request->query('name');

        $leaders = PolicyIntroducer::query()
            ->join('introducers', 'policy_introducers.introducer_id', '=', 'introducers.id')
            ->selectRaw("COALESCE(NULLIF(TRIM(CONCAT(introducers.name, ' ', introducers.middle_name, ' ', introducers.last_name)), ''), introducers.email, 'Unknown') as introducer_label")
            ->selectRaw('COUNT(DISTINCT policy_introducers.policy_id) as policies_count')
            ->groupBy('introducer_label')
            ->orderByDesc('policies_count')
            ->limit($limit)
            ->get();

        $policies = collect();
        if ($introducerName) {

            $introducer = \App\Models\Introducer::query()
                ->whereRaw("COALESCE(NULLIF(TRIM(CONCAT(name, ' ', middle_name, ' ', last_name)), ''), email) = ?", [$introducerName])
                ->first();

            if ($introducer) {
                $policyIds = PolicyIntroducer::query()
                    ->select('policy_id')
                    ->where('introducer_id', $introducer->id)
                    ->pluck('policy_id')
                    ->unique()
                    ->values();

                if ($policyIds->isNotEmpty()) {
                    $policies = Policy::query()->whereIn('id', $policyIds)->orderByDesc('id')->get();
                }
            }
        }

        return view('reports.top_introducers', compact('leaders', 'policies', 'introducerName', 'limit'));
    }

    public function statusReport(Request $request)
    {
        if ($request->ajax()) {
            $statusFilter = $request->query('filter_status');

            $query = Policy::query()
                ->select([
                    'policies.id',
                    'policies.policy_number',
                    'policies.status',
                    'policies.opening_date',
                    'policies.created_at',
                ])
                ->when($statusFilter, function ($q) use ($statusFilter) {
                    $q->whereRaw('LOWER(status) = ?', [strtolower($statusFilter)]);
                });

            return datatables()->of($query)
                ->addColumn('policy_status', fn($row) => strtoupper($row->status ?? ''))
                ->addColumn('effective_date', fn($row) =>
                    $row->opening_date ? Carbon::parse($row->opening_date)->format('Y-m-d') : null
                )
                ->addColumn('issue_date', fn($row) =>
                    $row->created_at ? $row->created_at->format('Y-m-d') : null
                )
                ->addColumn('anniversary_date', fn($row) =>
                    $row->opening_date ? Carbon::parse($row->opening_date)->addYear()->format('Y-m-d') : null
                )
                ->addColumn('idf_manager', function ($row) {
                    return InvestmentDedicatedFund::where('policy_id', $row->id)
                        ->select('type', 'name', 'first_name', 'middle_name', 'last_name')
                        ->where('user_type', 'manager')
                        ->get()
                        ->map(function ($fund) {
                            if ($fund->type === 'Individual') {
                                return collect([$fund->first_name, $fund->middle_name, $fund->last_name])
                                    ->filter()
                                    ->implode(' ');
                            }
                            return $fund->name;
                        })
                        ->implode('</br>');
                })
                ->addColumn('holders', function ($row) {
                    return PolicyHolder::where('policy_id', $row->id)
                        ->select('type', 'name', 'first_name', 'middle_name', 'last_name')
                        ->get()
                        ->map(function ($holder) {
                            return collect([$holder->first_name, $holder->middle_name, $holder->last_name])
                                ->filter()
                                ->implode(' ') ?: $holder->name;
                        })
                        ->implode('</br>');
                })
                ->addColumn('insureds', function ($row) {
                    return PolicyInsuredLifeInformation::where('policy_id', $row->id)
                        ->select('type', 'name', 'first_name', 'middle_name', 'last_name')
                        ->get()
                        ->map(function ($insured) {
                            return collect([$insured->first_name, $insured->middle_name, $insured->last_name])
                                ->filter()
                                ->implode(' ') ?: $insured->name;
                        })
                        ->implode('</br>');
                })
                ->addColumn('introducers', function ($row) {
                    return PolicyIntroducer::where('policy_id', $row->id)
                        ->select('type', 'name', 'middle_name', 'last_name')
                        ->get()
                        ->map(function ($intro) {
                            return collect([$intro->name, $intro->middle_name, $intro->last_name])
                                ->filter()
                                ->implode(' ') ?: $intro->name;
                        })
                        ->implode('</br>');
                })
                ->rawColumns(['idf_manager', 'holders', 'insureds', 'introducers'])
                ->make(true);
        }

        $title = 'Reports';
        $subTitle = 'Status Report';

        return view('reports.status-report', compact('title', 'subTitle'));
    }

    public function exportStatusReport(Request $request)
    {
        $status = $request->query('status');
        return Excel::download(new StatusReportExport($status), 'status_report.xlsx');
    }

    public function exportMissingDocsReport(Request $request)
    {
        return Excel::download(new MissingDocumentExport(), 'missing_documents_report.xlsx');
    }

    public function missingExpiredDocuments(Request $request)
    {
        if ($request->ajax()) {
            $today = Carbon::now()->toDateString();

            $query = PolicyDocument::query()
                ->select([
                    'policy_documents.id',
                    'policy_documents.policy_id',
                    'policy_documents.record_id',
                    'policy_documents.document_id',
                    'policy_documents.document_type',
                    'policy_documents.uploaded',
                    'policy_documents.has_expiry_date',
                    'policy_documents.expiry_date',
                ])
                ->where(function ($q) use ($today) {
                    $q->whereNull('uploaded')
                      ->orWhere('uploaded', 0)
                      ->orWhereNull('document')
                      ->orWhere('document', '=','')
                      ->orWhere(function ($q2) use ($today) {
                          $q2->where('has_expiry_date', 1)
                             ->whereNotNull('expiry_date')
                             ->where('expiry_date', '<', $today);
                      });
                });

            return datatables()->of($query)
                ->addColumn('policy_number', function ($row) {
                    return optional(Policy::find($row->policy_id))->policy_number;
                })
                ->addColumn('role', function ($row) {
                    $map = [
                        'policy-holder' => 'Policyholder',
                        'controlling-person' => 'Controlling Person',
                        'insured-life' => 'Insured Life',
                        'beneficiary' => 'Beneficiary',
                    ];
                    return $map[$row->document_type] ?? ucfirst(str_replace('-', ' ', $row->document_type));
                })
                ->addColumn('user', function ($row) {
                    switch ($row->document_type) {
                        case 'policy-holder':
                            $u = PolicyHolder::find($row->record_id);
                            break;
                        case 'controlling-person':
                            $u = PolicyController::find($row->record_id);
                            break;
                        case 'insured-life':
                            $u = PolicyInsuredLifeInformation::find($row->record_id);
                            break;
                        case 'beneficiary':
                            $u = \App\Models\PolicyBeneficiary::find($row->record_id);
                            break;
                        default:
                            $u = null;
                    }
                    if (!$u) return '';
                    $name = trim(collect([$u->first_name ?? null, $u->middle_name ?? null, $u->last_name ?? null])->filter()->implode(' '));
                    return $name !== '' ? $name : ($u->name ?? '');
                })
                ->addColumn('document', function ($row) {
                    return optional(Document::find($row->document_id))->title ?: '';
                })
                ->addColumn('traffic', function ($row) use ($today) {
                    $isMissing = !$row->uploaded || empty($row->document);
                    $isExpired = (int)$row->has_expiry_date === 1 && $row->expiry_date && $row->expiry_date < $today;
                    $color = $isExpired ? 'red' : ($isMissing ? 'amber' : 'green');
                    $title = $isExpired ? 'Expired' : ($isMissing ? 'Not Uploaded' : 'Valid');
                    return "<span class=\"tl-dot tl-{$color}\" title=\"{$title}\"></span>";
                })
                ->addColumn('expdt', function ($row) {
                    return $row->has_expiry_date && $row->expiry_date ? (date('d-m-Y', strtotime($row->expiry_date))) : 'N/A';
                })
                ->addColumn('status', function ($row) use ($today) {
                    $isMissing = !$row->uploaded || empty($row->document);
                    $isExpired = (int)$row->has_expiry_date === 1 && $row->expiry_date && $row->expiry_date < $today;
                    if ($isMissing) return 'NOT UPLOADED';
                    if ($isExpired) return 'EXPIRED';
                    return '';
                })
                ->rawColumns(['traffic'])
                ->make(true);
        }

        $title = 'Reports';
        $subTitle = 'Missing/Expired Documents';
        return view('reports.missing-expired-documents', compact('title', 'subTitle'));
    }
}


