<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DocumentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $individual = [
            'Certified or Notarized Passport' => ['has_expiry_date' => true],
            'Certified or Notarized Driver\'s License' => ['has_expiry_date' => true],
            'Certified or Notarized National Identification Card' => ['has_expiry_date' => true],
            'Certified or Notarized Proof of Address (English or Translated)' => ['has_expiry_date' => true, 'date_column_name' => 'POA as at date'],
            'Professional Reference Letter' => ['has_expiry_date' => false],
            'Bank Reference Letter' => ['has_expiry_date' => false],
            'Due Diligence Declaration' => ['has_expiry_date' => false],
            'Privacy Notice' => ['has_expiry_date' => false],
            'Term Sheet' => ['has_expiry_date' => false],
            'Structure Chart' => ['has_expiry_date' => false],
            'Tax Returns' => ['has_expiry_date' => false]
        ];

        foreach ($individual as $i => $v) {
            \App\Models\Document::updateOrCreate([
                'type' => 'policy-holders',
                'title' => $i,
                'status' => 'individual',
                'has_expiry_date' => $v['has_expiry_date'],
                'date_column_name' => isset($v['date_column_name']) ? $v['date_column_name'] : 'POA as at date'
            ]);
        }

        $trust = [
            'Certified or Notarized Trust Deed' => ['has_expiry_date' => false],
            'Certified or Notarized Passport for the Trustee' => ['has_expiry_date' => true],
            'Certified or Notarized National Identification Card for the Trustee' => ['has_expiry_date' => true],
            'Certified or Notarized Proof of Address for the Trustee (English or Translated)' => ['has_expiry_date' => true],
            'Certified or Notarized Passport for the Settlor' => ['has_expiry_date' => true],
            'Certified or Notarized National Identification for the Settlor' => ['has_expiry_date' => true],
            'Certified or Notarized Proof of Address for the Settlor (English or Translated)' => ['has_expiry_date' => true],
            'Certified or Notarized Passport for the Protector' => ['has_expiry_date' => true],
            'Certified or Notarized National Identification Card for the Protector' => ['has_expiry_date' => true],
            'Certified or Notarized Proof of Address for the Protector (English or Translated)' => ['has_expiry_date' => true],
        ];

        foreach ($trust as $t => $v) {
            \App\Models\Document::updateOrCreate([
                'type' => 'policy-holders',
                'title' => $t,
                'status' => 'trust',
                'has_expiry_date' => $v['has_expiry_date'],
                'date_column_name' => isset($v['date_column_name']) ? $v['date_column_name'] : 'POA as at date'
            ]);
        }

        $llc = [
            'Certified or Notarized Certificate of Good Standing' => ['has_expiry_date' => false],
            'Certified or Notarized Certificate of Incorporation' => ['has_expiry_date' => false],
            'Certified or Notarized Shareholder Register' => ['has_expiry_date' => false],
            'Certified or Notarized Director Register' => ['has_expiry_date' => false],
            'Certified or Notarized Memorandum of Articles or By-Laws' => ['has_expiry_date' => false],
            'Certified or Notarized Copy of Signatory List' => ['has_expiry_date' => false],
            'Certified or Notarized Passport for Each Signatory, Director and Shareholder' => ['has_expiry_date' => false],
            'Certified or Notarized Proof of Address for Each Signatory, Director and Shareholder (English or Translated)' => ['has_expiry_date' => false],
            'Privacy Notice' => ['has_expiry_date' => false],
        ];


        foreach ($llc as $l => $v) {
            \App\Models\Document::updateOrCreate([
                'type' => 'policy-holders',
                'title' => $l,
                'status' => 'llc',
                'has_expiry_date' => $v['has_expiry_date'],
                'date_column_name' => isset($v['date_column_name']) ? $v['date_column_name'] : 'POA as at date'
            ]);
        }



















        $ctrlprsn = [
            'Certified or Notarized Passport' => ['has_expiry_date' => true],
            'Certified or Notarized Driver\'s License' => ['has_expiry_date' => true],
            'Certified or Notarized National Identification Card' => ['has_expiry_date' => true],
            'Certified or Notarized Proof of Address (English or Translated)' => ['has_expiry_date' => true, 'date_column_name' => 'POA as at date'],
            'Professional Reference Letter' => ['has_expiry_date' => false],
            'Due Diligence Declaration' => ['has_expiry_date' => false],
            'Privacy Notice' => ['has_expiry_date' => false],
            'Term Sheet' => ['has_expiry_date' => false],
            'Structure Chart' => ['has_expiry_date' => false],
            'Tax Returns' => ['has_expiry_date' => false]
        ];

        foreach ($ctrlprsn as $cp => $v) {
            \App\Models\Document::updateOrCreate([
                'type' => 'controlling-person',
                'title' => $cp,
                'status' => '',
                'has_expiry_date' => $v['has_expiry_date'],
                'date_column_name' => isset($v['date_column_name']) ? $v['date_column_name'] : 'POA as at date'
            ]);
        }

        $il = [
            'Certified or Notarized Passport' => ['has_expiry_date' => true],
            'Certified or Notarized Driver\'s License' => ['has_expiry_date' => true],
            'Certified or Notarized National Identification Card' => ['has_expiry_date' => true],
            'Certified or Notarized Proof of Address (English or Translated)' => ['has_expiry_date' => true, 'date_column_name' => 'POA as at date'],
            'Due Diligence Declaration' => ['has_expiry_date' => false],
            'Privacy Notice' => ['has_expiry_date' => false],
            'Term Sheet' => ['has_expiry_date' => false],
            'Structure Chart' => ['has_expiry_date' => false],
            'Tax Returns' => ['has_expiry_date' => false]
        ];

        foreach ($il as $i => $v) {
            \App\Models\Document::updateOrCreate([
                'type' => 'insured-life',
                'title' => $i,
                'status' => '',
                'has_expiry_date' => $v['has_expiry_date'],
                'date_column_name' => isset($v['date_column_name']) ? $v['date_column_name'] : 'POA as at date'
            ]);
        }
        
        $ben = [
            'Certified or Notarized Passport' => ['has_expiry_date' => true],
            'Certified or Notarized Driver\'s License' => ['has_expiry_date' => true],
            'Certified or Notarized National Identification Card' => ['has_expiry_date' => true],
            'Certified or Notarized Proof of Address (English or Translated)' => ['has_expiry_date' => true, 'date_column_name' => 'POA as at date'],
            'Professional Reference Letter' => ['has_expiry_date' => false],
            'Due Diligence Declaration' => ['has_expiry_date' => false],
            'Privacy Notice' => ['has_expiry_date' => false],
            'Term Sheet' => ['has_expiry_date' => false],
            'Structure Chart' => ['has_expiry_date' => false],
            'Tax Returns' => ['has_expiry_date' => false]
        ];

        foreach ($ben as $b => $v) {
            \App\Models\Document::updateOrCreate([
                'type' => 'beneficiary',
                'title' => $b,
                'status' => '',
                'has_expiry_date' => $v['has_expiry_date'],
                'date_column_name' => isset($v['date_column_name']) ? $v['date_column_name'] : 'POA as at date'
            ]);
        }        

        $downloadableDocuments = [
            'Application Form',
            'Source of Wealth Form',
            'Source of Funds (for policy)',
            'W8 Ben',
            'W9',
            'Privacy Notice',
            'Medical Examination Form',
            'Term Sheet',
            'Structure Chart',
            '1035 Exchange Form',
            'Personal Information Authorization'
        ];

        foreach ($downloadableDocuments as $downloadableDocument) {
            \App\Models\DownloadableDocument::updateOrCreate([
                'title' => $downloadableDocument
            ]);
        }
    }
}
