<?php

namespace App\Exports;

use App\Models\PolicyDocument;
use App\Models\Policy;
use App\Models\PolicyHolder;
use App\Models\PolicyController;
use App\Models\PolicyInsuredLifeInformation;
use App\Models\PolicyBeneficiary;
use App\Models\Document;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class MissingDocumentExport implements FromCollection, WithHeadings
{
    public function collection()
    {
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
                  ->orWhere('document', '=' ,'')
                  ->orWhere(function ($q2) use ($today) {
                      $q2->where('has_expiry_date', 1)
                         ->whereNotNull('expiry_date')
                         ->where('expiry_date', '<', $today);
                  });
            })
            ->get();

        $data = $query->map(function ($row) use ($today) {
            return [
                'policy_id' => $row->policy_id,
                'record_id' => $row->record_id,
                'document_id' => $row->document_id,
                'document_type' => $row->document_type,
                'uploaded' => $row->uploaded,
                'has_expiry_date' => $row->has_expiry_date,
                'expiry_date' => $row->expiry_date,
                'policy_number' => optional(Policy::find($row->policy_id))->policy_number,
                'role' => $this->getRole($row),
                'user' => $this->getUser($row),
                'document' => optional(Document::find($row->document_id))->title ?: '',
                'status' => $this->getStatus($row, $today),
            ];
        });

        return $data;
    }

    public function headings(): array
    {
        return [
            'Policy ID',
            'Record ID',
            'Document ID',
            'Document Type',
            'Uploaded',
            'Has Expiry Date',
            'Expiry Date',
            'Policy Number',
            'Role',
            'User',
            'Document',
            'Status'
        ];
    }

    private function getRole($row)
    {
        $map = [
            'policy-holder' => 'Policyholder',
            'controlling-person' => 'Controlling Person',
            'insured-life' => 'Insured Life',
            'beneficiary' => 'Beneficiary',
        ];
        return $map[$row->document_type] ?? ucfirst(str_replace('-', ' ', $row->document_type));
    }

    private function getUser($row)
    {
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
                $u = PolicyBeneficiary::find($row->record_id);
                break;
            default:
                $u = null;
        }

        if (!$u) return '';
        
        $name = trim(collect([$u->first_name ?? null, $u->middle_name ?? null, $u->last_name ?? null])->filter()->implode(' '));
        return $name !== '' ? $name : ($u->name ?? '');
    }

    private function getStatus($row, $today)
    {
        $isMissing = !$row->uploaded || empty($row->document);
        $isExpired = (int)$row->has_expiry_date === 1 && $row->expiry_date && $row->expiry_date < $today;

        if ($isMissing) return 'NOT UPLOADED';
        if ($isExpired) return 'EXPIRED';
        return '';
    }
}
