<?php

namespace App\Exports;

use App\Models\Policy;
use App\Models\PolicyHolder;
use App\Models\PolicyIntroducer;
use App\Models\InvestmentDedicatedFund;
use App\Models\Liklihood;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class NewPoliciesExport implements FromCollection, WithHeadings, WithMapping
{
    protected string $range;

    public function __construct(string $range = 'ytd')
    {
        $this->range = strtolower($range);
    }

    public function collection()
    {
        $now = Carbon::now();
        $from = match ($this->range) {
            'qtd' => $now->copy()->firstOfQuarter(),
            'mtd' => $now->copy()->startOfMonth(),
            default => $now->copy()->startOfYear(),
        };

        return Policy::query()
            ->whereNotNull('opening_date')
            ->whereBetween('opening_date', [$from, $now])
            ->orderByDesc('opening_date')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Likelihood',
            'Policy Number',
            'Issue Date',
            'Effective Date',
            'Policyholders',
            'Introducer',
            'IDF Manager',
            'Status',
        ];
    }

    public function map($policy): array
    {
        $likelihood = optional(Liklihood::find($policy->liklihood))->name;
        $holders = optional(PolicyHolder::where('policy_id', $policy->id)->first())->name ?: 'N/A';
        $intro = optional(PolicyIntroducer::where('policy_id', $policy->id)->first())->name ?: 'N/A';

        $idf = InvestmentDedicatedFund::where('policy_id', $policy->id)
            ->where('user_type', 'manager')
            ->first();
        if ($idf) {
            if ($idf->type === 'Individual') {
                $idfMgr = collect([$idf->first_name, $idf->middle_name, $idf->last_name])->filter()->implode(' ');
            } else {
                $idfMgr = $idf->name ?: 'N/A';
            }
        } else {
            $idfMgr = 'N/A';
        }

        return [
            $likelihood,
            $policy->policy_number,
            $policy->opening_date ? Carbon::parse($policy->opening_date)->format('Y-m-d') : null,
            $policy->opening_date ? Carbon::parse($policy->opening_date)->format('Y-m-d') : null,
            $holders,
            $intro,
            $idfMgr,
            $policy->status,
        ];
    }
}
