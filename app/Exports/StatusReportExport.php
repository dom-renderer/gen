<?php

namespace App\Exports;

use App\Models\InvestmentDedicatedFund;
use App\Models\Policy;
use App\Models\PolicyHolder;
use App\Models\PolicyInsuredLifeInformation;
use App\Models\PolicyIntroducer;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class StatusReportExport implements FromCollection, WithHeadings
{
    protected ?string $statusFilter;

    public function __construct(?string $statusFilter = null)
    {
        $this->statusFilter = $statusFilter;
    }

    public function collection()
    {
        $policiesQuery = Policy::query();
        if (!empty($this->statusFilter)) {
            $policiesQuery->whereRaw('LOWER(status) = ?', [strtolower($this->statusFilter)]);
        }

        $policies = $policiesQuery->orderByDesc('id')->get(['id', 'policy_number', 'status', 'opening_date', 'created_at']);

        $rows = collect();

        foreach ($policies as $policy) {
            foreach (PolicyHolder::where('policy_id', $policy->id)->get() as $h) {
                $rows->push([
                    $policy->policy_number,
                    'policy-holder',
                    trim($h->type == 'Individual' ? collect([$h->name, $h->first_name, $h->middle_name, $h->last_name])->filter()->implode(' ') : $h->name) ?: ($h->email ?? 'Unknown'),
                    strtoupper($policy->status ?? ''),
                    optional($policy->created_at)->format('Y-m-d'),
                    optional($policy->opening_date ? Carbon::parse($policy->opening_date) : null)->format('Y-m-d'),
                    optional($policy->opening_date ? Carbon::parse($policy->opening_date)->addYear() : null)->format('Y-m-d')
                ]);
            }

            foreach (PolicyInsuredLifeInformation::where('policy_id', $policy->id)->get() as $ins) {
                $rows->push([
                    $policy->policy_number,
                    'insured',
                    trim(collect([$ins->name, $ins->middle_name, $ins->last_name])->filter()->implode(' ')) ?: ($ins->email ?? 'Unknown'),
                    strtoupper($policy->status ?? ''),
                    optional($policy->created_at)->format('Y-m-d'),
                    optional($policy->opening_date ? Carbon::parse($policy->opening_date) : null)->format('Y-m-d'),
                    optional($policy->opening_date ? Carbon::parse($policy->opening_date)->addYear() : null)->format('Y-m-d')
                ]);
            }

            foreach (PolicyIntroducer::where('policy_id', $policy->id)->get() as $intro) {
                $rows->push([
                    $policy->policy_number,
                    'introducer',
                    trim($intro->type == 'Individual' ? collect([$intro->name, $intro->middle_name, $intro->last_name])->filter()->implode(' ') : $intro->name) ?: ($h->email ?? 'Unknown'),
                    strtoupper($policy->status ?? ''),
                    optional($policy->created_at)->format('Y-m-d'),
                    optional($policy->opening_date ? Carbon::parse($policy->opening_date) : null)->format('Y-m-d'),
                    optional($policy->opening_date ? Carbon::parse($policy->opening_date)->addYear() : null)->format('Y-m-d')
                ]);
            }

            foreach (InvestmentDedicatedFund::where('policy_id', $policy->id)->where('user_type', 'manager')->get() as $intro) {
                $rows->push([
                    $policy->policy_number,
                    'introducer',
                    trim($intro->type == 'Individual' ? collect([$intro->first_name, $intro->middle_name, $intro->last_name])->filter()->implode(' ') : $intro->name) ?: ($h->email ?? 'Unknown'),
                    strtoupper($policy->status ?? ''),
                    optional($policy->created_at)->format('Y-m-d'),
                    optional($policy->opening_date ? Carbon::parse($policy->opening_date) : null)->format('Y-m-d'),
                    optional($policy->opening_date ? Carbon::parse($policy->opening_date)->addYear() : null)->format('Y-m-d')
                ]);
            }
        }

        return $rows;
    }

    public function headings(): array
    {
        return [
            'Policy Number',
            'Role',
            'User',
            'Policy Status',
            'Issue Date',
            'Effective Date',
            'Anniversary Date',
        ];
    }
}


