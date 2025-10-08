<?php

namespace App\Exports;

use App\Models\Policy;
use Illuminate\Support\Collection;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PoliciesByStatusExport implements FromCollection, WithHeadings, WithMapping
{
    protected ?string $status;

    /**
     * @param string|null $status Filter by status (optional)
     */
    public function __construct(?string $status = null)
    {
        $this->status = $status ? strtolower($status) : null;
    }

    public function collection()
    {
        $statuses = [
            'active', 'surrendered', 'terminated', 'prospect', '1035 EXCHANGE', 'under review', 'lapse', 'draft'
        ];

        $query = Policy::query()
            ->select(['id', 'policy_number', 'status', 'opening_date', 'created_at'])
            ->orderByDesc('id');

        if ($this->status) {
            $query->whereRaw('LOWER(status) = ?', [$this->status]);
        } else {
            $query->whereIn('status', $statuses);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            '#', 'Policy Number', 'Status', 'Opening Date', 'Created'
        ];
    }

    /**
     * @param \App\Models\Policy $policy
     */
    public function map($policy): array
    {
        $opening = $policy->opening_date ? Carbon::parse($policy->opening_date)->format('Y-m-d H:i') : null;
        $created = $policy->created_at ? Carbon::parse($policy->created_at)->format('Y-m-d') : null;

        return [
            $policy->id,
            $policy->policy_number,
            $policy->status,
            $opening,
            $created,
        ];
    }
}
