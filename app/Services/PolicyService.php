<?php

namespace App\Services;

use App\Models\PolicyFeeSummaryInternalAdminStep;
use App\Models\PolicyFeeSummaryCommissionSplit;
use App\Models\PolicyCountryOfTaxResidence;
use App\Models\Custodian;
use App\Models\InvestmentDedicatedFund;
use App\Models\InvestmentAdvisor;
use App\Models\PolicyCommunication;
use App\Models\PolicyBeneficiary;
use App\Models\PolicyController;
use App\Jobs\MailboxJob;
use App\Models\PolicyForm;
use App\Models\PolicyDocument;
use App\Models\UploadableDocument;
use App\Models\PolicyEconomicProfile;
use App\Models\PolicyFeeSummaryExternal;
use App\Models\PolicyFeeSummaryInternalFee;
use App\Models\PolicyHolder;
use App\Models\PolicyInsuredLifeInformation;
use App\Models\PolicyInvestmentNote;
use App\Models\PolicyPremium;
use App\Models\PolicyIntroducer;
use App\Models\PolicyIntroducerContactPerson;
use App\Models\Policy;
use App\Models\PolicyCaseFileNote;
use App\Models\PolicyFeeSummaryInternal;
use App\Models\PolicyInception;
use App\Models\PolicyOnGoing;

class PolicyService {

    public static $sections = [
        'section-a-1',
        'section-a-2',
        'section-b-1',
        'section-b-2',
        'section-c-1',
        'section-d-1',
        'section-e-1',
        'section-e-2',
        'section-e-3',
        'section-e-4',
        'section-f-1',
        'section-f-2',
        'section-f-3',
        'section-f-4',
        'section-f-5',
        'section-f-6',
        'section-f-7',
        'section-g-1',
        'section-g-2',
        'section-h-1',
        'section-h-2',
    ];

    public static $sectionsTitle = [
        'section-a-1' => 'Introducer Profile',
        'section-a-2' => 'Key Parties & Roles',
        'section-b-1' => 'Policyholder Profile',
        'section-b-2' => 'Controlling Person',
        'section-c-1' => 'Insured life Information',
        'section-d-1' => 'Beneficiary Profile',
        'section-e-1' => 'Policyholder Required Documents',
        'section-e-2' => 'Controlling Person Required Documents',
        'section-e-3' => 'Insured Life/ves Required Documents',
        'section-e-4' => 'Beneficiary/ies Required Documents',
        'section-f-1' => 'Economic Profile',
        'section-f-2' => 'Premium',
        'section-f-3' => 'Fee Summary (Internal)',
        'section-f-4' => 'Fee Summary (External)',
        'section-f-5' => 'Investment Profile (Inception)',
        'section-f-6' => 'Investment Profile (Ongoing',
        'section-f-7' => 'Investment Profile (Investment Notes)',
        'section-g-1' => 'Communications',
        'section-g-2' => 'Case File Notes',
        'section-h-1' => 'Downloable Forms',
        'section-h-2' => 'Upload Forms',
    ];

    public function submit($request, $isAutoSave = false) : mixed
    {
        if ($request->filled('policy')) {
            $policy = Policy::find($request->policy);
            $savingType = $request->save != 'draft' ? 'save' : 'draft';
            $section = $request->section;
            $currentLoggedInUser = auth()->id();

            $response = [
                'data' => [],
                'type' => $savingType,
                'next_section' => $section
            ];

            if (in_array($section, self::$sections)) {
                MailboxJob::dispatch($policy, [
                    'title' => $sectionsTitle[$section] ?? 'Policy'
                ]);
            }

            if ($policy) {
                switch ($section) {
                case self::$sections[0]:

                    $request = (object)$request->data;

                    $introducerId = null;
                    $isAddNewIntroducer = isset($request->introducer_id) && $request->introducer_id === 'ADD_NEW_INTRODUCER';
                    if (isset($request->introducer_id) && is_numeric($request->introducer_id)) {
                        $introducerId = (int)$request->introducer_id;
                    }

                    if (!$introducerId && !$isAddNewIntroducer) {
                        return [
                            'errors' => [
                                'introducer_id' => ['Introducer is required']
                            ]
                        ];
                    }

                    $masterIntroducer = $introducerId ? \App\Models\Introducer::with('contacts')->find($introducerId) : null;
                    if ($introducerId && !$masterIntroducer) {
                        return [
                            'errors' => [
                                'introducer_id' => ['Selected introducer not found']
                            ]
                        ];
                    }

                    $type = strtolower($request->section_a_1_entity);
                    $name = $type === 'entity' ? ($request->section_a_1_name ?? null) : ($request->section_a_1_first_name ?? null);
                    $middle = $type === 'entity' ? '' : ($request->section_a_1_middle_name ?? null);
                    $last = $type === 'entity' ? '' : ($request->section_a_1_last_name ?? null);
                    $email = $request->section_a_1_email ?? null;
                    $dial = $request->section_a_1_dial_code ?? '41';
                    $phone = $request->section_a_1_phone ?? null;

                    if ($isAddNewIntroducer) {
                        $masterIntroducer = \App\Models\Introducer::create([
                            'type' => $type,
                            'name' => $name,
                            'middle_name' => $middle,
                            'last_name' => $last,
                            'email' => $email,
                            'dial_code' => $dial,
                            'contact_number' => $phone,
                        ]);
                    } else {
                        $masterIntroducer->update([
                            'type' => $type,
                            'name' => $name,
                            'middle_name' => $middle,
                            'last_name' => $last,
                            'email' => $email,
                            'dial_code' => $dial,
                            'contact_number' => $phone,
                        ]);
                    }

                    $policyIntroducerPayload = [
                        'policy_id' => $policy->id,
                        'introducer_id' => $masterIntroducer->id,
                        'added_by' => $currentLoggedInUser
                    ];

                    if (isset($request->id) && $request->id) {
                        $introducer = PolicyIntroducer::find($request->id);
                        if ($introducer) {
                            $introducer->update(array_merge($policyIntroducerPayload, [
                                'updated_by' => $currentLoggedInUser
                            ]));
                        }
                    } else {
                        $introducer = PolicyIntroducer::create($policyIntroducerPayload);
                    }

                    if ($type === 'entity' && isset($request->contact_person_first_name)) {
                        $keepMasterContactIds = [];
                        foreach ($request->contact_person_first_name as $index => $firstName) {
                            $cpId = $request->contact_person_id[$index] ?? null;
                            $cpEmail = $request->contact_person_email[$index] ?? null;
                            $cpPayload = [
                                'introducer_id' => $masterIntroducer->id,
                                'name' => $firstName ?: null,
                                'middle_name' => $request->contact_person_middle_name[$index] ?? null,
                                'last_name' => $request->contact_person_last_name[$index] ?? null,
                                'email' => $cpEmail,
                                'dial_code' => $request->contact_person_phone_number_dial_code[$index] ?? null,
                                'contact_number' => $request->contact_person_phone_number[$index] ?? null,
                            ];

                            if (!empty($cpId)) {
                                $model = \App\Models\IntroducerContactPerson::where('introducer_id', $masterIntroducer->id)->where('id', $cpId)->first();
                                if ($model) {
                                    $model->fill($cpPayload)->save();
                                    $keepMasterContactIds[] = $model->id;
                                }
                            } else if (!empty($firstName) || !empty($cpEmail)) {
                                $created = \App\Models\IntroducerContactPerson::create($cpPayload);
                                $keepMasterContactIds[] = $created->id;
                            }
                        }

                        if (!empty($keepMasterContactIds)) {
                            \App\Models\IntroducerContactPerson::where('introducer_id', $masterIntroducer->id)->whereNotIn('id', $keepMasterContactIds)->delete();
                        } else {
                            \App\Models\IntroducerContactPerson::where('introducer_id', $masterIntroducer->id)->delete();
                        }

                        $policyLinks = [];
                        foreach ($keepMasterContactIds as $mid) {
                            $policyLinks[] = [
                                'policy_id' => $policy->id,
                                'introducer_id' => $introducer->id,
                                'introducer_contact_person_id' => $mid,
                                'created_at' => now(),
                                'updated_at' => now()
                            ];
                        }

                        PolicyIntroducerContactPerson::where('introducer_id', $introducer->id)->delete();
                        if (!empty($policyLinks)) {
                            PolicyIntroducerContactPerson::insert($policyLinks);
                        }
                    } else {
                        PolicyIntroducerContactPerson::where('introducer_id', $introducer->id)->delete();
                    }

                    if (isset($request->add_more_a1) && $request->add_more_a1 == 1) {
                        $response['next_section'] = self::$sections[0];
                    } else {
                        $response['next_section'] = self::$sections[1];
                    }

                    return $response;
                case self::$sections[1]:

                    $toBeKeptPolicyHolders = [];

                    if ($request->has('data.policy_holder')) {
                        foreach ($request['data']['policy_holder'] as $index => $policyholder) {
                            if (!empty($policyholder['type'])) {
                                $name = '';
                                if ($policyholder['type'] === 'Individual') {
                                    $name = trim(($policyholder['first_name'] ?? '') . ' ' . ($policyholder['middle_name'] ?? '') . ' ' . ($policyholder['last_name'] ?? ''));
                                } else {
                                    $name = $policyholder['name'] ?? '';
                                }
                                
                                $toBeKeptPolicyHolders[] = PolicyHolder::updateOrCreate([
                                    'policy_id' => $policy->id,
                                    'full_name' => $name,
                                    'first_name' => $policyholder['first_name'] ?? '',
                                    'middle_name' => $policyholder['middle_name'] ?? '',
                                    'last_name' => $policyholder['last_name'] ?? '',
                                    'entity_type' => $policyholder['type'] ?? '',
                                    'notes' => $policyholder['notes'] ?? '',
                                ])->id;
                            }
                        }
                    }

                    if (!empty($toBeKeptPolicyHolders)) {
                        PolicyHolder::where('policy_id', $policy->id)->whereNotIn('id', $toBeKeptPolicyHolders)->delete();
                    } else {
                        PolicyHolder::where('policy_id', $policy->id)->delete();
                    }
                    
                    if ($request->has('data.insured_life') && is_array($request['data']['insured_life'])) {
                        $keepInsuredIds = [];
                        foreach ($request['data']['insured_life'] as $index => $insuredLife) {
                            if (!empty($insuredLife['entity_type'])) {
                                $name = '';
                                if (($insuredLife['entity_type'] ?? '') === 'Individual') {
                                    $name = trim(($insuredLife['first_name'] ?? '') . ' ' . ($insuredLife['middle_name'] ?? '') . ' ' . ($insuredLife['last_name'] ?? ''));
                                } else {
                                    $name = $insuredLife['name'] ?? '';
                                }

                                $payload = [
                                    'policy_id' => $policy->id,
                                    'full_name' => $name,
                                    'first_name' => $insuredLife['first_name'] ?? null,
                                    'middle_name' => $insuredLife['middle_name'] ?? null,
                                    'last_name' => $insuredLife['last_name'] ?? null,
                                    'entity_type' => $insuredLife['entity_type'] ?? null,
                                    'notes' => $insuredLife['notes'] ?? null,
                                ];

                                if (!empty($insuredLife['id'])) {
                                    $model = PolicyInsuredLifeInformation::where('policy_id', $policy->id)->where('id', $insuredLife['id'])->first();
                                    if ($model) {
                                        $model->fill($payload)->save();
                                        $keepInsuredIds[] = $model->id;
                                    } else {
                                        $created = PolicyInsuredLifeInformation::create($payload);
                                        $keepInsuredIds[] = $created->id;
                                    }
                                } else {
                                    $created = PolicyInsuredLifeInformation::create($payload);
                                    $keepInsuredIds[] = $created->id;
                                }
                            }
                        }

                        if (!empty($keepInsuredIds)) {
                            PolicyInsuredLifeInformation::where('policy_id', $policy->id)->whereNotIn('id', $keepInsuredIds)->delete();
                        } else {
                            PolicyInsuredLifeInformation::where('policy_id', $policy->id)->delete();
                        }
                    }

                    if ($request->has('data.beneficiary') && is_array($request['data']['beneficiary'])) {
                        $keepBeneficiaryIds = [];
                        foreach ($request['data']['beneficiary'] as $index => $beneficiary) {
                            if (!empty($beneficiary['entity_type'])) {
                                $name = '';
                                if (($beneficiary['entity_type'] ?? '') === 'Individual') {
                                    $name = trim(($beneficiary['first_name'] ?? '') . ' ' . ($beneficiary['middle_name'] ?? '') . ' ' . ($beneficiary['last_name'] ?? ''));
                                } else {
                                    $name = $beneficiary['name'] ?? '';
                                }

                                $payload = [
                                    'policy_id' => $policy->id,
                                    'full_name' => $name,
                                    'first_name' => $beneficiary['first_name'] ?? null,
                                    'middle_name' => $beneficiary['middle_name'] ?? null,
                                    'last_name' => $beneficiary['last_name'] ?? null,
                                    'entity_type' => $beneficiary['entity_type'] ?? null,
                                    'notes' => $beneficiary['notes'] ?? null,
                                ];

                                if (!empty($beneficiary['id'])) {
                                    $model = PolicyBeneficiary::where('policy_id', $policy->id)->where('id', $beneficiary['id'])->first();
                                    if ($model) {
                                        $model->fill($payload)->save();
                                        $keepBeneficiaryIds[] = $model->id;
                                    } else {
                                        $created = PolicyBeneficiary::create($payload);
                                        $keepBeneficiaryIds[] = $created->id;
                                    }
                                } else {
                                    $created = PolicyBeneficiary::create($payload);
                                    $keepBeneficiaryIds[] = $created->id;
                                }
                            }
                        }

                        if (!empty($keepBeneficiaryIds)) {
                            PolicyBeneficiary::where('policy_id', $policy->id)->whereNotIn('id', $keepBeneficiaryIds)->delete();
                        } else {
                            PolicyBeneficiary::where('policy_id', $policy->id)->delete();
                        }
                    }
                    
                    if ($request->has('data.advisor_applicable') && $request['data']['advisor_applicable'] === 'not_applicable') {
                        InvestmentAdvisor::where('policy_id', $policy->id)->delete();
                    } elseif ($request->has('data.advisor') && is_array($request['data']['advisor'])) {
                        $keepAdvisorIds = [];
                        foreach ($request['data']['advisor'] as $index => $advisor) {
                            if (!empty($advisor['entity_type'])) {
                                $name = '';
                                if (($advisor['entity_type'] ?? '') === 'Individual') {
                                    $name = trim(($advisor['first_name'] ?? '') . ' ' . ($advisor['middle_name'] ?? '') . ' ' . ($advisor['last_name'] ?? ''));
                                } else {
                                    $name = $advisor['name'] ?? '';
                                }

                                $payload = [
                                    'policy_id' => $policy->id,
                                    'name' => $name,
                                    'first_name' => $advisor['first_name'] ?? null,
                                    'middle_name' => $advisor['middle_name'] ?? null,
                                    'last_name' => $advisor['last_name'] ?? null,
                                    'type' => $advisor['entity_type'] ?? null,
                                    'notes' => $advisor['notes'] ?? null,
                                ];

                                if (!empty($advisor['id'])) {
                                    $model = InvestmentAdvisor::where('policy_id', $policy->id)->where('id', $advisor['id'])->first();
                                    if ($model) {
                                        $model->fill($payload)->save();
                                        $keepAdvisorIds[] = $model->id;
                                    } else {
                                        $created = InvestmentAdvisor::create($payload);
                                        $keepAdvisorIds[] = $created->id;
                                    }
                                } else {
                                    $created = InvestmentAdvisor::create($payload);
                                    $keepAdvisorIds[] = $created->id;
                                }
                            }
                        }

                        if (!empty($keepAdvisorIds)) {
                            InvestmentAdvisor::where('policy_id', $policy->id)->whereNotIn('id', $keepAdvisorIds)->delete();
                        } else {
                            InvestmentAdvisor::where('policy_id', $policy->id)->delete();
                        }
                    }
                    
                    if ($request->has('data.idf_applicable') && $request['data']['idf_applicable'] === 'not_applicable') {
                        InvestmentDedicatedFund::where('policy_id', $policy->id)->where('user_type', 'name')->delete();
                    } elseif ($request->has('data.idf') && is_array($request['data']['idf'])) {
                        $keepIdfIds = [];
                        foreach ($request['data']['idf'] as $index => $idf) {
                            if (!empty($idf['entity_type'])) {
                                $name = '';
                                if (($idf['entity_type'] ?? '') === 'Individual') {
                                    $name = trim(($idf['first_name'] ?? '') . ' ' . ($idf['middle_name'] ?? '') . ' ' . ($idf['last_name'] ?? ''));
                                } else {
                                    $name = $idf['name'] ?? '';
                                }

                                $payload = [
                                    'policy_id' => $policy->id,
                                    'user_type' => 'name',
                                    'name' => $name,
                                    'first_name' => $idf['first_name'] ?? null,
                                    'middle_name' => $idf['middle_name'] ?? null,
                                    'last_name' => $idf['last_name'] ?? null,
                                    'type' => $idf['entity_type'] ?? null,
                                    'notes' => $idf['notes'] ?? null,
                                ];

                                if (!empty($idf['id'])) {
                                    $model = InvestmentDedicatedFund::where('policy_id', $policy->id)->where('user_type', 'name')->where('id', $idf['id'])->first();
                                    if ($model) {
                                        $model->fill($payload)->save();
                                        $keepIdfIds[] = $model->id;
                                    } else {
                                        $created = InvestmentDedicatedFund::create($payload);
                                        $keepIdfIds[] = $created->id;
                                    }
                                } else {
                                    $created = InvestmentDedicatedFund::create($payload);
                                    $keepIdfIds[] = $created->id;
                                }
                            }
                        }

                        if (!empty($keepIdfIds)) {
                            InvestmentDedicatedFund::where('policy_id', $policy->id)->where('user_type', 'name')->whereNotIn('id', $keepIdfIds)->delete();
                        } else {
                            InvestmentDedicatedFund::where('policy_id', $policy->id)->where('user_type', 'name')->delete();
                        }
                    }
                    
                    if ($request->has('data.idf_manager_applicable') && $request['data']['idf_manager_applicable'] === 'not_applicable') {
                        InvestmentDedicatedFund::where('policy_id', $policy->id)->where('user_type', 'manager')->delete();
                    } elseif ($request->has('data.idf_manager') && is_array($request['data']['idf_manager'])) {
                        $keepIdfMgrIds = [];
                        foreach ($request['data']['idf_manager'] as $index => $idfManager) {
                            if (!empty($idfManager['entity_type'])) {
                                $name = '';
                                if (($idfManager['entity_type'] ?? '') === 'Individual') {
                                    $name = trim(($idfManager['first_name'] ?? '') . ' ' . ($idfManager['middle_name'] ?? '') . ' ' . ($idfManager['last_name'] ?? ''));
                                } else {
                                    $name = $idfManager['name'] ?? '';
                                }

                                $payload = [
                                    'policy_id' => $policy->id,
                                    'user_type' => 'manager',
                                    'name' => $name,
                                    'first_name' => $idfManager['first_name'] ?? null,
                                    'middle_name' => $idfManager['middle_name'] ?? null,
                                    'last_name' => $idfManager['last_name'] ?? null,
                                    'type' => $idfManager['entity_type'] ?? null,
                                    'notes' => $idfManager['notes'] ?? null,
                                ];

                                if (!empty($idfManager['id'])) {
                                    $model = InvestmentDedicatedFund::where('policy_id', $policy->id)->where('user_type', 'manager')->where('id', $idfManager['id'])->first();
                                    if ($model) {
                                        $model->fill($payload)->save();
                                        $keepIdfMgrIds[] = $model->id;
                                    } else {
                                        $created = InvestmentDedicatedFund::create($payload);
                                        $keepIdfMgrIds[] = $created->id;
                                    }
                                } else {
                                    $created = InvestmentDedicatedFund::create($payload);
                                    $keepIdfMgrIds[] = $created->id;
                                }
                            }
                        }

                        if (!empty($keepIdfMgrIds)) {
                            InvestmentDedicatedFund::where('policy_id', $policy->id)->where('user_type', 'manager')->whereNotIn('id', $keepIdfMgrIds)->delete();
                        } else {
                            InvestmentDedicatedFund::where('policy_id', $policy->id)->where('user_type', 'manager')->delete();
                        }
                    }
                    
                    if ($request->has('data.custodian_applicable') && $request['data']['custodian_applicable'] === 'not_applicable') {
                        Custodian::where('policy_id', $policy->id)->delete();
                    } elseif ($request->has('data.custodian') && is_array($request['data']['custodian'])) {
                        $keepCustodianIds = [];
                        foreach ($request['data']['custodian'] as $index => $custodian) {
                            if (!empty($custodian['entity_type'])) {
                                $name = '';
                                if (($custodian['entity_type'] ?? '') === 'Individual') {
                                    $name = trim(($custodian['first_name'] ?? '') . ' ' . ($custodian['middle_name'] ?? '') . ' ' . ($custodian['last_name'] ?? ''));
                                } else {
                                    $name = $custodian['name'] ?? '';
                                }

                                $payload = [
                                    'policy_id' => $policy->id,
                                    'name' => $name,
                                    'first_name' => $custodian['first_name'] ?? null,
                                    'middle_name' => $custodian['middle_name'] ?? null,
                                    'last_name' => $custodian['last_name'] ?? null,
                                    'type' => $custodian['entity_type'] ?? null,
                                    'notes' => $custodian['notes'] ?? null,
                                ];

                                if (!empty($custodian['id'])) {
                                    $model = Custodian::where('policy_id', $policy->id)->where('id', $custodian['id'])->first();
                                    if ($model) {
                                        $model->fill($payload)->save();
                                        $keepCustodianIds[] = $model->id;
                                    } else {
                                        $created = Custodian::create($payload);
                                        $keepCustodianIds[] = $created->id;
                                    }
                                } else {
                                    $created = Custodian::create($payload);
                                    $keepCustodianIds[] = $created->id;
                                }
                            }
                        }

                        if (!empty($keepCustodianIds)) {
                            Custodian::where('policy_id', $policy->id)->whereNotIn('id', $keepCustodianIds)->delete();
                        } else {
                            Custodian::where('policy_id', $policy->id)->delete();
                        }
                    }

                    Policy::where('id', $policy->id)->update([
                        'investment_advisor_manager_applicable' => isset($request['data']['advisor_applicable']) && $request['data']['advisor_applicable'] == 'applicable' ? 1 : 0,
                        'idf_name_applicable' => isset($request['data']['idf_applicable']) && $request['data']['idf_applicable'] == 'applicable' ? 1 : 0,
                        'idf_manager_applicable' => isset($request['data']['idf_manager_applicable']) && $request['data']['idf_manager_applicable'] == 'applicable' ? 1 : 0,
                        'custodian_applicable' => isset($request['data']['custodian_applicable']) && $request['data']['custodian_applicable'] == 'applicable' ? 1 : 0
                    ]);

                    $response['next_section'] = self::$sections[2];
                    return $response;
                case self::$sections[2]:

                        if (empty($request['data']['name']) && empty($request['data']['first_name'])) {
                            return ['errors' => ['name' => 'Name is required']];
                        }

                        if (is_numeric($request['data']['policy_holder_id']) && $request['data']['policy_holder_id'] > 0) {

                            $customer = PolicyHolder::find($request['data']['policy_holder_id']);

                            if ($customer) {

                                $customer->entity_type = $request['data']['type'] ?? 'entity';
                                $customer->first_name = $request['data']['first_name'] ?? ($customer->first_name ?? '');
                                $customer->middle_name = $request['data']['middle_name'] ?? ($customer->middle_name ?? '');
                                $customer->last_name = $request['data']['last_name'] ?? ($customer->last_name ?? '');
                                $customer->full_name = $request['data']['name'] ?? ($customer->full_name ?? '');
                                $customer->email = $request['data']['email'] ?? '';
                                $customer->place_of_birth = $request['data']['place_of_birth'] ?? '';
                                $customer->date_of_birth = isset($request['data']['dob']) ? date('Y-m-d', strtotime($request['data']['dob'])) : null;
                                $customer->country = $request['data']['country'] ?? '';
                                $customer->city = $request['data']['city'] ?? '';
                                $customer->zipcode = $request['data']['zipcode'] ?? '';
                                $customer->dial_code = $request['data']['dial_code'] ?? ($customer->dial_code ?? '');
                                $customer->phone_number = $request['data']['phone_number'] ?? ($customer->phone_number ?? '');
                                $customer->address_line_1 = $request['data']['address_line_1'] ?? '';
                                $customer->personal_status = $request['data']['entity_status'] ?? $request['data']['marital_status'] ?? 'corporation';
                                $customer->personal_status_other = $request['data']['entity_status_other'] ?? '';
                                $customer->national_country_of_registration = $request['data']['national_country_of_registration'] ?? '';
                                $customer->gender = $request['data']['gender'] ?? 'male';
                                $customer->country_of_legal_residence = $request['data']['country_of_legal_residence'] ?? '';

                                if (isset($request['data']['passport_number']) && is_array($request['data']['passport_number'])) {
                                    $customer->passport_number = json_encode(array_filter($request['data']['passport_number']));
                                } else {
                                    $customer->passport_number = $request['data']['passport_number'] ?? '';
                                }
                                if (isset($request['data']['country_of_issuance']) && is_array($request['data']['country_of_issuance'])) {
                                    $customer->country_of_issuance = json_encode(array_filter($request['data']['country_of_issuance']));
                                } else {
                                    $customer->country_of_issuance = $request['data']['country_of_issuance'] ?? '';
                                }
                                if (isset($request['data']['tin']) && is_array($request['data']['tin'])) {
                                    $customer->tin = json_encode(array_filter($request['data']['tin']));
                                } else {
                                    $customer->tin = $request['data']['tin'] ?? '';
                                }
                                $customer->lei = $request['data']['lei'] ?? '';
                                $customer->email = $request['data']['email'] ?? '';
                                $customer->save();

                                $toKeepLocations = [];

                                if (isset($request['data']['all_countries']) && is_array($request['data']['all_countries'])) {
                                    foreach ($request['data']['all_countries'] as $thisCountry) {
                                        if (!empty($thisCountry)) {
                                            $toKeepLocations[] = PolicyCountryOfTaxResidence::updateOrCreate([
                                                'eloquent' => PolicyHolder::class,
                                                'policy_id' => $policy->id,
                                                'eloquent_id' => $customer->id,
                                                'country' => $thisCountry
                                            ])->id;
                                        }
                                    }
                                }

                                if (!empty($toKeepLocations)) {
                                    PolicyCountryOfTaxResidence::where('policy_id', $policy->id)->where('eloquent', PolicyHolder::class)->whereNotIn('id', $toKeepLocations)->delete();
                                } else {
                                    PolicyCountryOfTaxResidence::where('policy_id', $policy->id)->where('eloquent', PolicyHolder::class)->delete();
                                }
                            }
                            
                        } else if (empty($request['data']['policy_holder_id']) || $request['data']['policy_holder_id'] == '') {

                                $customer = new PolicyHolder();
                                $customer->policy_id = $policy->id;
                                $customer->entity_type = $request['data']['type'] ?? 'Corporate';
                                $customer->first_name = $request['data']['first_name'] ?? '';
                                $customer->middle_name = $request['data']['middle_name'] ?? '';
                                $customer->last_name = $request['data']['last_name'] ?? '';
                                $customer->full_name = $request['data']['name'] ?? '';
                                $customer->email = $request['data']['email'] ?? '';
                                $customer->place_of_birth = $request['data']['place_of_birth'] ?? '';
                                $customer->date_of_birth = isset($request['data']['dob']) ? date('Y-m-d', strtotime($request['data']['dob'])) : null;
                                $customer->country = $request['data']['country'] ?? '';
                                $customer->city = $request['data']['city'] ?? '';
                                $customer->zipcode = $request['data']['zipcode'] ?? '';
                                $customer->dial_code = $request['data']['dial_code'] ?? '';
                                $customer->phone_number = $request['data']['phone_number'] ?? '';
                                $customer->address_line_1 = $request['data']['address_line_1'] ?? '';
                                $customer->personal_status = $request['data']['entity_status'] ?? $request['data']['marital_status'] ?? 'corporation';
                                $customer->personal_status_other = $request['data']['entity_status_other'] ?? '';
                                $customer->national_country_of_registration = $request['data']['national_country_of_registration'] ?? '';
                                $customer->gender = $request['data']['gender'] ?? 'male';
                                $customer->country_of_legal_residence = $request['data']['country_of_legal_residence'] ?? '';
                            
                                if (isset($request['data']['passport_number']) && is_array($request['data']['passport_number'])) {
                                    $customer->passport_number = json_encode(array_filter($request['data']['passport_number']));
                                } else {
                                    $customer->passport_number = $request['data']['passport_number'] ?? '';
                                }
                                if (isset($request['data']['country_of_issuance']) && is_array($request['data']['country_of_issuance'])) {
                                    $customer->country_of_issuance = json_encode(array_filter($request['data']['country_of_issuance']));
                                } else {
                                    $customer->country_of_issuance = $request['data']['country_of_issuance'] ?? '';
                                }
                                if (isset($request['data']['tin']) && is_array($request['data']['tin'])) {
                                    $customer->tin = json_encode(array_filter($request['data']['tin']));
                                } else {
                                    $customer->tin = $request['data']['tin'] ?? '';
                                }
                                $customer->lei = $request['data']['lei'] ?? '';
                                $customer->save();

                                $toKeepLocations = [];

                                if (isset($request['data']['all_countries']) && is_array($request['data']['all_countries'])) {
                                    foreach ($request['data']['all_countries'] as $thisCountry) {
                                        if (!empty($thisCountry)) {
                                            $toKeepLocations[] = PolicyCountryOfTaxResidence::updateOrCreate([
                                                'eloquent' => PolicyHolder::class,
                                                'policy_id' => $policy->id,
                                                'eloquent_id' => $customer->id,
                                                'country' => $thisCountry
                                            ])->id;
                                        }
                                    }
                                }

                                if (!empty($toKeepLocations)) {
                                    PolicyCountryOfTaxResidence::where('policy_id', $policy->id)->where('eloquent', PolicyHolder::class)->whereNotIn('id', $toKeepLocations)->delete();
                                } else {
                                    PolicyCountryOfTaxResidence::where('policy_id', $policy->id)->where('eloquent', PolicyHolder::class)->delete();
                                }
                        }

                    if (isset($request->add_more_a1) && $request->add_more_a1 == 1) {
                        $response['next_section'] = self::$sections[2];
                    } else {
                        $response['next_section'] = self::$sections[3];
                    }
                    return $response;
                case self::$sections[3]:

                    if (PolicyController::where('policy_id', $policy->id)->exists()) {
                        $updatePayload = [
                            'updated_by' => $currentLoggedInUser,
                            'first_name' => $request['data']['first_name'] ?? '',
                            'middle_name' => $request['data']['middle_name'] ?? '',
                            'last_name' => $request['data']['last_name'] ?? '',
                            'place_of_birth' => $request['data']['place_of_birth'] ?? '',
                            'date_of_birth' => isset($request['data']['dob']) ? date('Y-m-d', strtotime($request['data']['dob'])) : null,
                            'address_line_1' => $request['data']['address_line_1'] ?? '',
                            'zipcode' => $request['data']['zipcode'] ?? '',
                            'country' => $request['data']['country'] ?? '',
                            'city' => $request['data']['city'] ?? '',
                            'personal_status' => $request['data']['status'] ?? 'single',
                            'smoker_status' => $request['data']['smoker_status'] ?? 'non-smoker',
                            'national_country_of_registration' => $request['data']['national_country_of_registration'] ?? '',
                            'gender' => $request['data']['gender'] ?? 'male',
                            'country_of_legal_residence' => $request['data']['country_of_legal_residence'] ?? '',
                            'passport_number' => isset($request['data']['passport_number']) && is_array($request['data']['passport_number']) ? json_encode(array_filter($request['data']['passport_number'])) : ($request['data']['passport_number'] ?? ''),
                            'country_of_issuance' => isset($request['data']['country_of_issuance']) && is_array($request['data']['country_of_issuance']) ? json_encode(array_filter($request['data']['country_of_issuance'])) : ($request['data']['country_of_issuance'] ?? ''),
                            'email' => $request['data']['email'] ?? '',
                            'relationship_to_policyholder' => $request['data']['relationship_to_policyholder'] ?? ''
                        ];
                        PolicyController::where('policy_id', $policy->id)->update($updatePayload);

                        $pcntrlr = PolicyController::where('policy_id', $policy->id)->first();
                    } else {
                        $pcntrlr = PolicyController::create([
                            'policy_id' => $policy->id,
                            'added_by' => $currentLoggedInUser,
                            'first_name' => $request['data']['first_name'] ?? '',
                            'middle_name' => $request['data']['middle_name'] ?? '',
                            'last_name' => $request['data']['last_name'] ?? '',
                            'place_of_birth' => $request['data']['place_of_birth'] ?? '',
                            'date_of_birth' => isset($request['data']['dob']) ? date('Y-m-d', strtotime($request['data']['dob'])) : null,
                            'address_line_1' => $request['data']['address_line_1'] ?? '',
                            'zipcode' => $request['data']['zipcode'] ?? '',
                            'country' => $request['data']['country'] ?? '',
                            'city' => $request['data']['city'] ?? '',
                            'personal_status' => $request['data']['status'] ?? 'single',
                            'smoker_status' => $request['data']['smoker_status'] ?? 'non-smoker',
                            'national_country_of_registration' => $request['data']['national_country_of_registration'] ?? '',
                            'gender' => $request['data']['gender'] ?? 'male',
                            'country_of_legal_residence' => $request['data']['country_of_legal_residence'] ?? '',
                            'passport_number' => isset($request['data']['passport_number']) && is_array($request['data']['passport_number']) ? json_encode(array_filter($request['data']['passport_number'])) : ($request['data']['passport_number'] ?? ''),
                            'country_of_issuance' => isset($request['data']['country_of_issuance']) && is_array($request['data']['country_of_issuance']) ? json_encode(array_filter($request['data']['country_of_issuance'])) : ($request['data']['country_of_issuance'] ?? ''),
                            'email' => $request['data']['email'] ?? '',
                            'relationship_to_policyholder' => $request['data']['relationship_to_policyholder'] ?? ''
                        ]);
                    }

                    $toKeepLocations = [];

                    if (isset($request['data']['all_countries']) && is_array($request['data']['all_countries']) && isset($pcntrlr->id)) {
                        foreach ($request['data']['all_countries'] as $thisCountry) {
                            if (!empty($thisCountry)) {
                                $toKeepLocations[] = PolicyCountryOfTaxResidence::updateOrCreate([
                                    'eloquent' => PolicyController::class,
                                    'policy_id' => $policy->id,
                                    'eloquent_id' => $pcntrlr->id,
                                    'country' => $thisCountry
                                ])->id;
                            }
                        }
                    }

                    if (!empty($toKeepLocations)) {
                        PolicyCountryOfTaxResidence::where('policy_id', $policy->id)->where('eloquent', PolicyController::class)->whereNotIn('id', $toKeepLocations)->delete();
                    } else {
                        PolicyCountryOfTaxResidence::where('policy_id', $policy->id)->where('eloquent', PolicyController::class)->delete();
                    }

                    if (isset($request->add_more_a1) && $request->add_more_a1 == 1) {
                        $response['next_section'] = self::$sections[3];
                    } else {
                        $response['next_section'] = self::$sections[4];
                    }
                    return $response;
                case self::$sections[4]:

                    if (isset($request['data']) && is_string($request['data'])) {
                        $request['data'] = json_decode($request['data'], true);

                        $insuredLifeData = [
                            'policy_id' => $policy->id,
                            'entity_type' => $request['data']['c1_type'] ?? 'individual',
                            'full_name' => $request['data']['controlling_person_name'] ?? '',
                            'first_name' => $request['data']['first_name'] ?? '',
                            'middle_name' => $request['data']['middle_name'] ?? '',
                            'last_name' => $request['data']['last_name'] ?? '',
                            'place_of_birth' => $request['data']['place_of_birth'] ?? '',
                            'date_of_birth' => isset($request['data']['date_of_birth']) ? date('Y-m-d', strtotime($request['data']['date_of_birth'])) : null,
                            'address_line_1' => $request['data']['address'] ?? '',
                            'zipcode' => $request['data']['zip'] ?? '',
                            'country' => $request['data']['country'] ?? '',
                            'city' => $request['data']['city'] ?? '',
                            'personal_status' => $request['data']['status'] ?? 'single',
                            'smoker_status' => $request['data']['smoker_status'] ?? 'smoker',
                            'national_country_of_registration' => $request['data']['nationality'] ?? '',
                            'gender' => $request['data']['gender'] ?? 'male',
                            'country_of_legal_residence' => $request['data']['country_of_legal_residence'] ?? '',
                            'passport_number' => isset($request['data']['passport_number']) && is_array($request['data']['passport_number']) ? json_encode(array_filter($request['data']['passport_number'])) : ($request['data']['passport_number'] ?? ''),
                            'country_of_issuance' => isset($request['data']['country_of_issuance']) && is_array($request['data']['country_of_issuance']) ? json_encode(array_filter($request['data']['country_of_issuance'])) : ($request['data']['country_of_issuance'] ?? ''),
                            'relationship_to_policyholder' => $request['data']['relationship_to_policyholder'] ?? '',
                            'email' => $request['data']['email'] ?? ''
                        ];

                        if (isset($request['data']['id']) && $request['data']['id']) {
                            $theId = $request['data']['id'];
                            $insuredLife = PolicyInsuredLifeInformation::find($request['data']['id']);
                            if ($insuredLife) {
                                $insuredLife->update(array_merge($insuredLifeData, [
                                    'updated_by' => $currentLoggedInUser
                                ]));
                            }
                        } else {
                            $insuredLifeData['added_by'] = $currentLoggedInUser;
                            $theId = PolicyInsuredLifeInformation::create($insuredLifeData)->id;
                        }

                        $toKeepLocations = [];

                        if (isset($request['data']['all_countries']) && is_array($request['data']['all_countries']) && isset($theId)) {
                            foreach ($request['data']['all_countries'] as $thisCountry) {
                                if (!empty($thisCountry)) {
                                    $toKeepLocations[] = PolicyCountryOfTaxResidence::updateOrCreate([
                                        'eloquent' => PolicyInsuredLifeInformation::class,
                                        'policy_id' => $policy->id,
                                        'eloquent_id' => $theId,
                                        'country' => $thisCountry
                                    ])->id;
                                }
                            }
                        }

                        if (!empty($toKeepLocations)) {
                            PolicyCountryOfTaxResidence::where('policy_id', $policy->id)->where('eloquent', PolicyInsuredLifeInformation::class)->whereNotIn('id', $toKeepLocations)->delete();
                        } else {
                            PolicyCountryOfTaxResidence::where('policy_id', $policy->id)->where('eloquent', PolicyInsuredLifeInformation::class)->delete();
                        }

                    } else if (isset($request['data']) && is_array($request['data'])) {

                        $insuredLifeData = [
                            'policy_id' => $policy->id,
                            'entity_type' => $request['data']['c1_type'] ?? 'individual',
                            'full_name' => $request['data']['controlling_person_name'] ?? '',
                            'first_name' => $request['data']['first_name'] ?? '',
                            'middle_name' => $request['data']['middle_name'] ?? '',
                            'last_name' => $request['data']['last_name'] ?? '',
                            'place_of_birth' => $request['data']['place_of_birth'] ?? '',
                            'date_of_birth' => isset($request['data']['date_of_birth']) ? date('Y-m-d', strtotime($request['data']['date_of_birth'])) : null,
                            'address_line_1' => $request['data']['address'] ?? '',
                            'zipcode' => $request['data']['zip'] ?? '',
                            'country' => $request['data']['country'] ?? '',
                            'city' => $request['data']['city'] ?? '',
                            'personal_status' => $request['data']['status'] ?? 'single',
                            'smoker_status' => $request['data']['smoker_status'] ?? 'smoker',
                            'national_country_of_registration' => $request['data']['nationality'] ?? '',
                            'gender' => $request['data']['gender'] ?? 'male',
                            'country_of_legal_residence' => $request['data']['country_of_legal_residence'] ?? '',
                            'passport_number' => isset($request['data']['passport_number']) && is_array($request['data']['passport_number']) ? json_encode(array_filter($request['data']['passport_number'])) : ($request['data']['passport_number'] ?? ''),
                            'country_of_issuance' => isset($request['data']['country_of_issuance']) && is_array($request['data']['country_of_issuance']) ? json_encode(array_filter($request['data']['country_of_issuance'])) : ($request['data']['country_of_issuance'] ?? ''),
                            'relationship_to_policyholder' => $request['data']['relationship_to_policyholder'] ?? '',
                            'email' => $request['data']['email'] ?? ''
                        ];

                        if (isset($request['data']['id']) && $request['data']['id']) {
                            $theId = $request['data']['id'];
                            $insuredLife = PolicyInsuredLifeInformation::find($request['data']['id']);
                            if ($insuredLife) {
                                $insuredLife->update(array_merge($insuredLifeData, [
                                    'updated_by' => $currentLoggedInUser
                                ]));
                            }
                        } else {
                            $insuredLifeData['added_by'] = $currentLoggedInUser;
                            $theId = PolicyInsuredLifeInformation::create($insuredLifeData)->id;
                        }

                        $toKeepLocations = [];

                        if (isset($request['data']['all_countries']) && is_array($request['data']['all_countries']) && isset($theId)) {
                            foreach ($request['data']['all_countries'] as $thisCountry) {
                                if (!empty($thisCountry)) {
                                    $toKeepLocations[] = PolicyCountryOfTaxResidence::updateOrCreate([
                                        'eloquent' => PolicyInsuredLifeInformation::class,
                                        'policy_id' => $policy->id,
                                        'eloquent_id' => $theId,
                                        'country' => $thisCountry
                                    ])->id;
                                }
                            }
                        }

                        if (!empty($toKeepLocations)) {
                            PolicyCountryOfTaxResidence::where('policy_id', $policy->id)->where('eloquent', PolicyInsuredLifeInformation::class)->whereNotIn('id', $toKeepLocations)->delete();
                        } else {
                            PolicyCountryOfTaxResidence::where('policy_id', $policy->id)->where('eloquent', PolicyInsuredLifeInformation::class)->delete();
                        }
                    }

                    if (isset($request->add_more_a1) && $request->add_more_a1 == 1) {
                        $response['next_section'] = self::$sections[4];
                    } else {
                        $response['next_section'] = self::$sections[5];
                    }
                    return $response;
                case self::$sections[5]:


                    if (isset($request['data']) && is_string($request['data'])) {
                        $request['data'] = json_decode($request['data'], true);

                        $insuredLifeData = [
                            'policy_id' => $policy->id,
                            'entity_type' => $request['data']['d1_type'] ?? 'individual',
                            'full_name' => $request['data']['full_name'] ?? '',
                            'first_name' => $request['data']['first_name'] ?? '',
                            'middle_name' => $request['data']['middle_name'] ?? '',
                            'last_name' => $request['data']['last_name'] ?? '',
                            'place_of_birth' => $request['data']['place_of_birth'] ?? '',
                            'date_of_birth' => isset($request['data']['date_of_birth']) ? date('Y-m-d', strtotime($request['data']['date_of_birth'])) : null,
                            'address_line_1' => $request['data']['address'] ?? '',
                            'zipcode' => $request['data']['zip'] ?? '',
                            'country' => $request['data']['country'] ?? '',
                            'city' => $request['data']['city'] ?? '',
                            'personal_status' => $request['data']['status'] ?? 'single',
                            'national_country_of_registration' => $request['data']['nationality'] ?? '',
                            'gender' => $request['data']['gender'] ?? 'male',
                            'country_of_legal_residence' => $request['data']['country_of_legal_residence'] ?? '',
                            'passport_number' => isset($request['data']['passport_number']) && is_array($request['data']['passport_number']) ? json_encode(array_filter($request['data']['passport_number'])) : ($request['data']['passport_number'] ?? ''),
                            'country_of_issuance' => isset($request['data']['country_of_issuance']) && is_array($request['data']['country_of_issuance']) ? json_encode(array_filter($request['data']['country_of_issuance'])) : ($request['data']['country_of_issuance'] ?? ''),
                            'relationship_to_policyholder' => $request['data']['relationship_to_policyholder'] ?? '',
                            'phone_number' => $request['data']['phone_number'] ?? '',
                            'dial_code' => $request['data']['dial_code'] ?? '',
                            'beneficiary_death_benefit_allocation' => $request['data']['beneficiary_death_benefit_allocation'] ?? '',
                            'designation_of_beneficiary' => $request['data']['designation_of_beneficiary'] ?? 0,
                            'nationality' => $request['data']['nationality'],
                            'email' => $request['data']['email'] ?? ''
                        ];

                        if (isset($request['data']['id']) && $request['data']['id']) {
                            $theId = $request['data']['id'];
                            $insuredLife = PolicyBeneficiary::find($request['data']['id']);
                            if ($insuredLife) {
                                $insuredLife->update(array_merge($insuredLifeData, [
                                    'updated_by' => $currentLoggedInUser
                                ]));
                            }
                        } else {
                            $insuredLifeData['added_by'] = $currentLoggedInUser;
                            $theId = PolicyBeneficiary::create($insuredLifeData)->id;
                        }

                        $toKeepLocations = [];

                        if (isset($request['data']['all_countries']) && is_array($request['data']['all_countries']) && isset($theId)) {
                            foreach ($request['data']['all_countries'] as $thisCountry) {
                                if (!empty($thisCountry)) {
                                    $toKeepLocations[] = PolicyCountryOfTaxResidence::updateOrCreate([
                                        'eloquent' => PolicyBeneficiary::class,
                                        'policy_id' => $policy->id,
                                        'eloquent_id' => $theId,
                                        'country' => $thisCountry
                                    ])->id;
                                }
                            }
                        }

                        if (!empty($toKeepLocations)) {
                            PolicyCountryOfTaxResidence::where('policy_id', $policy->id)->where('eloquent', PolicyBeneficiary::class)->whereNotIn('id', $toKeepLocations)->delete();
                        } else {
                            PolicyCountryOfTaxResidence::where('policy_id', $policy->id)->where('eloquent', PolicyBeneficiary::class)->delete();
                        }

                    } else if (isset($request['data']) && is_array($request['data'])) {

                        $insuredLifeData = [
                            'policy_id' => $policy->id,
                            'entity_type' => $request['data']['c1_type'] ?? 'individual',
                            'full_name' => $request['data']['controlling_person_name'] ?? '',
                            'first_name' => $request['data']['first_name'] ?? '',
                            'middle_name' => $request['data']['middle_name'] ?? '',
                            'last_name' => $request['data']['last_name'] ?? '',
                            'place_of_birth' => $request['data']['place_of_birth'] ?? '',
                            'date_of_birth' => isset($request['data']['date_of_birth']) ? date('Y-m-d', strtotime($request['data']['date_of_birth'])) : null,
                            'address_line_1' => $request['data']['address'] ?? '',
                            'zipcode' => $request['data']['zip'] ?? '',
                            'country' => $request['data']['country'] ?? '',
                            'city' => $request['data']['city'] ?? '',
                            'personal_status' => $request['data']['status'] ?? 'single',
                            'smoker_status' => $request['data']['smoker_status'] ?? 'smoker',
                            'national_country_of_registration' => $request['data']['nationality'] ?? '',
                            'gender' => $request['data']['gender'] ?? 'male',
                            'country_of_legal_residence' => $request['data']['country_of_legal_residence'] ?? '',
                            'passport_number' => isset($request['data']['passport_number']) && is_array($request['data']['passport_number']) ? json_encode(array_filter($request['data']['passport_number'])) : ($request['data']['passport_number'] ?? ''),
                            'country_of_issuance' => isset($request['data']['country_of_issuance']) && is_array($request['data']['country_of_issuance']) ? json_encode(array_filter($request['data']['country_of_issuance'])) : ($request['data']['country_of_issuance'] ?? ''),
                            'relationship_to_policyholder' => $request['data']['relationship_to_policyholder'] ?? '',
                            'phone_number' => $request['data']['phone_number'] ?? '',
                            'dial_code' => $request['data']['dial_code'] ?? '',
                            'beneficiary_death_benefit_allocation' => $request['data']['beneficiary_death_benefit_allocation'] ?? '',
                            'designation_of_beneficiary' => $request['data']['designation_of_beneficiary'] ?? 0,
                            'email' => $request['data']['email'] ?? ''
                        ];

                        if (isset($request['data']['id']) && $request['data']['id']) {
                            $theId = $request['data']['id'];
                            $insuredLife = PolicyBeneficiary::find($request['data']['id']);
                            if ($insuredLife) {
                                $insuredLife->update(array_merge($insuredLifeData, [
                                    'updated_by' => $currentLoggedInUser
                                ]));
                            }
                        } else {
                            $insuredLifeData['added_by'] = $currentLoggedInUser;
                            $theId = PolicyBeneficiary::create($insuredLifeData)->id;
                        }

                        $toKeepLocations = [];

                        if (isset($request['data']['all_countries']) && is_array($request['data']['all_countries']) && isset($theId)) {
                            foreach ($request['data']['all_countries'] as $thisCountry) {
                                if (!empty($thisCountry)) {
                                    $toKeepLocations[] = PolicyCountryOfTaxResidence::updateOrCreate([
                                        'eloquent' => PolicyBeneficiary::class,
                                        'policy_id' => $policy->id,
                                        'eloquent_id' => $theId,
                                        'country' => $thisCountry
                                    ])->id;
                                }
                            }
                        }

                        if (!empty($toKeepLocations)) {
                            PolicyCountryOfTaxResidence::where('policy_id', $policy->id)->where('eloquent', PolicyBeneficiary::class)->whereNotIn('id', $toKeepLocations)->delete();
                        } else {
                            PolicyCountryOfTaxResidence::where('policy_id', $policy->id)->where('eloquent', PolicyBeneficiary::class)->delete();
                        }
                    }

                    if (isset($request->add_more_a1) && $request->add_more_a1 == 1) {
                        $response['next_section'] = self::$sections[5];
                    } else {
                        $response['next_section'] = self::$sections[6];
                    }
                    return $response;
                case self::$sections[6]:

                    $keepChecked = [];

                    if (isset($request['data']['documents']) && is_array($request['data']['documents'])) {
                        foreach ($request['data']['documents'] as $recordId => $documents) {
                            if (!is_array($documents)) continue;
                            foreach ($documents as $documentId => $value) {
                                $hasExpiry = isset($request['data']['has_expiry_date'][$recordId][$documentId]) ? 1 : 0;
                                $expiryDate = $hasExpiry ? ($request['data']['expiry_date'][$recordId][$documentId] ?? null) : null;
                                $keepChecked[] = PolicyDocument::updateOrCreate([
                                    'policy_id' => $policy->id,
                                    'document_id' => $documentId,
                                    'document_type' => 'policy-holder',
                                    'record_id' => is_numeric($recordId) ? intval($recordId) : null,
                                ], [
                                    'uploaded' => 1,
                                    'has_expiry_date' => $hasExpiry,
                                    'expiry_date' => $expiryDate
                                ])->id;
                            }
                        }
                    }

                    if (!empty($keepChecked)) {
                        PolicyDocument::where('policy_id', $policy->id)
                            ->where('document_type', 'policy-holder')
                            ->whereNotIn('id', $keepChecked)
                            ->update(['uploaded' => 0]);
                    } else {
                        PolicyDocument::where('policy_id', $policy->id)
                            ->where('document_type', 'policy-holder')
                            ->update(['uploaded' => 0]);
                    }

                    $response['next_section'] = self::$sections[7];
                    return $response;
                case self::$sections[7]:

                    $keepChecked = [];

                    if (isset($request['data']['documents'])) {
                        foreach ($request['data']['documents'] as $document) {
                            $hasExpiry = isset($request['data']['has_expiry_date'][$document]) ? 1 : 0;
                            $expiryDate = $hasExpiry ? ($request['data']['expiry_date'][$document] ?? null) : null;
                            $keepChecked[] = PolicyDocument::updateOrCreate([
                                'policy_id' => $policy->id,
                                'document_id' => $document,
                                'document_type' => 'controlling-person',
                            ], [
                                'uploaded' => 1,
                                'has_expiry_date' => $hasExpiry,
                                'expiry_date' => $expiryDate
                            ])->id;
                        }
                    }

                    if (!empty($keepChecked)) {
                        PolicyDocument::where('policy_id', $policy->id)->where('document_type', 'controlling-person')->whereNotIn('id', $keepChecked)->update(['uploaded' => 0]);
                    } else {
                        PolicyDocument::where('policy_id', $policy->id)->where('document_type', 'controlling-person')->update(['uploaded' => 0]);
                    }

                    $response['next_section'] = self::$sections[8];
                    return $response;
                case self::$sections[8]:

                    $keepChecked = [];

                    if (isset($request['data']['documents']) && is_array($request['data']['documents'])) {
                        foreach ($request['data']['documents'] as $recordId => $documents) {
                            if (!is_array($documents)) continue;
                            foreach ($documents as $documentId => $value) {
                                $hasExpiry = isset($request['data']['has_expiry_date'][$recordId][$documentId]) ? 1 : 0;
                                $expiryDate = $hasExpiry ? ($request['data']['expiry_date'][$recordId][$documentId] ?? null) : null;
                                $keepChecked[] = PolicyDocument::updateOrCreate([
                                    'policy_id' => $policy->id,
                                    'document_id' => $documentId,
                                    'document_type' => 'insured-life',
                                    'record_id' => is_numeric($recordId) ? intval($recordId) : null,
                                ], [
                                    'uploaded' => 1,
                                    'has_expiry_date' => $hasExpiry,
                                    'expiry_date' => $expiryDate
                                ])->id;
                            }
                        }
                    }

                    if (!empty($keepChecked)) {
                        PolicyDocument::where('policy_id', $policy->id)
                            ->where('document_type', 'insured-life')
                            ->whereNotIn('id', $keepChecked)
                            ->update(['uploaded' => 0]);
                    } else {
                        PolicyDocument::where('policy_id', $policy->id)
                            ->where('document_type', 'insured-life')
                            ->update(['uploaded' => 0]);
                    }

                    $response['next_section'] = self::$sections[9];
                    return $response;
                case self::$sections[9]:

                    $keepChecked = [];

                    if (isset($request['data']['documents']) && is_array($request['data']['documents'])) {
                        foreach ($request['data']['documents'] as $recordId => $documents) {
                            if (!is_array($documents)) continue;
                            foreach ($documents as $documentId => $value) {
                                $hasExpiry = isset($request['data']['has_expiry_date'][$recordId][$documentId]) ? 1 : 0;
                                $expiryDate = $hasExpiry ? ($request['data']['expiry_date'][$recordId][$documentId] ?? null) : null;
                                $keepChecked[] = PolicyDocument::updateOrCreate([
                                    'policy_id' => $policy->id,
                                    'document_id' => $documentId,
                                    'document_type' => 'beneficiary',
                                    'record_id' => is_numeric($recordId) ? intval($recordId) : null,
                                ], [
                                    'uploaded' => 1,
                                    'has_expiry_date' => $hasExpiry,
                                    'expiry_date' => $expiryDate
                                ])->id;
                            }
                        }
                    }

                    if (!empty($keepChecked)) {
                        PolicyDocument::where('policy_id', $policy->id)
                            ->where('document_type', 'beneficiary')
                            ->whereNotIn('id', $keepChecked)
                            ->update(['uploaded' => 0]);
                    } else {
                        PolicyDocument::where('policy_id', $policy->id)
                            ->where('document_type', 'beneficiary')
                            ->update(['uploaded' => 0]);
                    }

                    $response['next_section'] = self::$sections[10];
                    return $response;
                case self::$sections[10]:
                    if (PolicyEconomicProfile::where('policy_id', $policy->id)->exists()) {
                        PolicyEconomicProfile::where('policy_id', $policy->id)->update([
                            'purpose_of_policy_and_structure' => isset($request['data']['purpose']) ? ( is_string($request['data']['purpose']) ? [$request['data']['purpose']] : (is_array($request['data']['purpose']) ? $request['data']['purpose'] : []) ) : [],
                            'additional_details' => $request['data']['additional_details'] ?? '',
                            'estimated_networth' => $request['data']['estimated_networth'] ?? '',
                            'source_of_wealth_for_policy' => $request['data']['source_of_wealth_for_policy'] ?? '',
                            'distribution_strategy_during_policy_lifetime' => $request['data']['distribution_strategy_during_policy_lifetime'] ?? '',
                            'distribution_strategy_post_death_payout' => $request['data']['distribution_strategy_post_death_payout'] ?? '',
                            'known_triggers_for_policy_exit_or_surrender' => $request['data']['known_triggers_for_policy_exit_or_surrender'] ?? '',
                            'updated_by' => $currentLoggedInUser
                        ]);
                    } else {
                        PolicyEconomicProfile::create([
                            'policy_id' => $policy->id,
                            'purpose_of_policy_and_structure' => isset($request['data']['purpose']) ? ( is_string($request['data']['purpose']) ? [$request['data']['purpose']] : (is_array($request['data']['purpose']) ? $request['data']['purpose'] : []) ) : [],
                            'additional_details' => $request['data']['additional_details'] ?? '',
                            'estimated_networth' => $request['data']['estimated_networth'] ?? '',
                            'source_of_wealth_for_policy' => $request['data']['source_of_wealth_for_policy'] ?? '',
                            'distribution_strategy_during_policy_lifetime' => $request['data']['distribution_strategy_during_policy_lifetime'] ?? '',
                            'distribution_strategy_post_death_payout' => $request['data']['distribution_strategy_post_death_payout'] ?? '',
                            'known_triggers_for_policy_exit_or_surrender' => $request['data']['known_triggers_for_policy_exit_or_surrender'] ?? '',
                            'added_by' => $currentLoggedInUser
                        ]);
                    }

                    $response['next_section'] = self::$sections[11];
                    return $response;
                case self::$sections[11]:

                    if (PolicyPremium::where('policy_id', $policy->id)->exists()) {
                        PolicyPremium::where('policy_id', $policy->id)->update([
                            'policy_type' => $request['data']['type'] ?? '',
                            'proposed_premium_amount' => $request['data']['proposed_premium'] ?? '',
                            'proposed_premium_note' => $request['data']['proposed_premium_note'] ?? '',
                            'final_premium_amount' => $request['data']['final_premium'] ?? '',
                            'final_premium_note' => $request['data']['final_premium_note'] ?? '',
                            'premium_frequency' => $request['data']['premium_frequency'] ?? '',
                            'updated_by' => $currentLoggedInUser
                        ]);
                    } else {
                        PolicyPremium::create([
                            'policy_id' => $policy->id,
                            'policy_type' => $request['data']['type'] ?? '',
                            'proposed_premium_amount' => $request['data']['proposed_premium'] ?? '',
                            'proposed_premium_note' => $request['data']['proposed_premium_note'] ?? '',
                            'final_premium_amount' => $request['data']['final_premium'] ?? '',
                            'final_premium_note' => $request['data']['final_premium_note'] ?? '',
                            'premium_frequency' => $request['data']['premium_frequency'] ?? '',
                            'added_by' => $currentLoggedInUser
                        ]);
                    }

                    $response['next_section'] = self::$sections[12];
                    return $response;
                case self::$sections[12]:

                    if (PolicyFeeSummaryInternal::where('policy_id', $policy->id)->exists()) {
                        $created = PolicyFeeSummaryInternal::where('policy_id', $policy->id)->update([
                            'fee_provided_by' => $request['data']['fee_provided_by'] ?? '',
                            'date_fee_provided' => isset($request['data']['fee_provided_by_date']) ? date('Y-m-d H:i:s', strtotime($request['data']['fee_provided_by_date'])) : null,
                            'controlling_person_fee_approved_by' => $request['data']['fee_approved_by'] ?? '',
                            'date_fee_approved' => isset($request['data']['fee_approved_by_date']) ? date('Y-m-d H:i:s', strtotime($request['data']['fee_approved_by_date'])) : null,
                            'gii_fee_approved_by' => $request['data']['gii_fee_approved_by'] ?? '',
                            'gii_date_fee_approved' => isset($request['data']['gii_fee_approved_by_date']) ? date('Y-m-d H:i:s', strtotime($request['data']['gii_fee_approved_by_date'])) : null,
                            'fee_approval_notes' => $request['data']['approval_notes'] ?? '',
                            'updated_by' => $currentLoggedInUser
                        ]);
                        $created = PolicyFeeSummaryInternal::where('policy_id', $policy->id)->first();
                    } else {
                        $created = PolicyFeeSummaryInternal::create([
                            'policy_id' => $policy->id,
                            'fee_provided_by' => $request['data']['fee_provided_by'] ?? '',
                            'date_fee_provided' => isset($request['data']['fee_provided_by_date']) ? date('Y-m-d H:i:s', strtotime($request['data']['fee_provided_by_date'])) : null,
                            'controlling_person_fee_approved_by' => $request['data']['fee_approved_by'] ?? '',
                            'date_fee_approved' => isset($request['data']['fee_approved_by_date']) ? date('Y-m-d H:i:s', strtotime($request['data']['fee_approved_by_date'])) : null,
                            'gii_fee_approved_by' => $request['data']['gii_fee_approved_by'] ?? '',
                            'gii_date_fee_approved' => isset($request['data']['gii_fee_approved_by_date']) ? date('Y-m-d H:i:s', strtotime($request['data']['gii_fee_approved_by_date'])) : null,
                            'fee_approval_notes' => $request['data']['approval_notes'] ?? '',
                            'added_by' => $currentLoggedInUser
                        ]);
                    }

                    if ($created) {
                        if (isset($request['data']['admin_fee'])) {
                            $admin = $request['data']['admin_fee'];

                            PolicyFeeSummaryInternal::where('id', $created->id)->update([
                                'admin_fee_type' => $admin['type'] ?? null,
                                'admin_fee_applied_to' => $admin['applied_to'] ?? null,
                                'admin_fee_limit' => $admin['limit'] ?? null,
                            ]);

                            if (isset($admin['steps']) && is_array($admin['steps'])) {
                                PolicyFeeSummaryInternalAdminStep::where('policy_fee_summary_internal_id', $created->id)->delete();
                                $pos = 1;
                                foreach ($admin['steps'] as $step) {
                                    if (!is_array($step)) continue;
                                    if (($step['from'] ?? '') === '' && ($step['to'] ?? '') === '' && ($step['rate'] ?? '') === '') continue;
                                    PolicyFeeSummaryInternalAdminStep::create([
                                        'policy_fee_summary_internal_id' => $created->id,
                                        'position' => $pos++,
                                        'from_value' => $step['from'] ?? null,
                                        'to_value' => $step['to'] ?? null,
                                        'rate_or_amount' => $step['rate'] ?? null,
                                    ]);
                                }
                            }
                        }
                        if ($request->has('data.a')) {
                            $setupFeeOption = $request['data']['fee_option']['set_up_fee'] ?? null;
                            $keyRoles1 = PolicyFeeSummaryInternalFee::where('policy_fee_summary_internal_id', $created->id)->where('type', $request['data']['a']['type'])->first();
                            if ($keyRoles1) {
                                PolicyFeeSummaryInternalFee::where('policy_fee_summary_internal_id', $created->id)->where('type', $request['data']['a']['type'])->update([
                                    'type' => $request['data']['a']['type'],
                                    'frequency' => $request['data']['a']['frequency'] ?? '',
                                    'amount' => $request['data']['a']['amount'] ?? 0,
                                    'rate' => $request['data']['a']['rate'] ?? 0,
                                    'notes' => $request['data']['a']['note'] ?? '',
                                    'fee_option' => $setupFeeOption,
                                    'updated_by' => $currentLoggedInUser
                                ]);

                                if (isset($request['data']['a']['commission_split']) && is_array($request['data']['a']['commission_split'])) {
                                    foreach ($request['data']['a']['commission_split'] as $intId => $comRow) {
                                        PolicyFeeSummaryCommissionSplit::updateOrCreate([
                                            'policy_id' => $policy->id,
                                            'policy_fee_summary_internal_fee_id' => $keyRoles1->id,
                                            'policy_introducer_id' => $intId,
                                        ],[
                                            'policy_id' => $policy->id,
                                            'policy_fee_summary_internal_fee_id' => $keyRoles1->id,
                                            'policy_introducer_id' => $intId,
                                            'commission' => $comRow
                                        ]);
                                    }
                                }
                            } else {
                                $keyRoles1 = PolicyFeeSummaryInternalFee::create([
                                    'policy_fee_summary_internal_id' => $created->id,
                                    'type' => $request['data']['a']['type'],
                                    'frequency' => $request['data']['a']['frequency'] ?? '',
                                    'amount' => $request['data']['a']['amount'] ?? 0,
                                    'rate' => $request['data']['a']['rate'] ?? 0,
                                    'notes' => $request['data']['a']['note'] ?? '',
                                    'fee_option' => $setupFeeOption,
                                    'added_by' => $currentLoggedInUser
                                ]);

                                if (isset($request['data']['a']['commission_split']) && is_array($request['data']['a']['commission_split'])) {
                                    foreach ($request['data']['a']['commission_split'] as $intId => $comRow) {
                                        PolicyFeeSummaryCommissionSplit::updateOrCreate([
                                            'policy_id' => $policy->id,
                                            'policy_fee_summary_internal_fee_id' => $keyRoles1->id,
                                            'policy_introducer_id' => $intId,
                                        ],[
                                            'policy_id' => $policy->id,
                                            'policy_fee_summary_internal_fee_id' => $keyRoles1->id,
                                            'policy_introducer_id' => $intId,
                                            'commission' => $comRow
                                        ]);
                                    }
                                }
                            }
                        }

                        if ($request->has('data.b')) {
                            $keyRoles1 = PolicyFeeSummaryInternalFee::where('policy_fee_summary_internal_id', $created->id)->where('type', $request['data']['b']['type'])->first();
                            if ($keyRoles1) {
                                PolicyFeeSummaryInternalFee::where('policy_fee_summary_internal_id', $created->id)->where('type', $request['data']['b']['type'])->update([
                                    'type' => $request['data']['b']['type'],
                                    'frequency' => $request['data']['b']['frequency'] ?? '',
                                    'amount' => $request['data']['b']['amount'] ?? 0,
                                    'rate' => $request['data']['b']['rate'] ?? 0,
                                    'notes' => $request['data']['b']['note'] ?? '',
                                    'updated_by' => $currentLoggedInUser
                                ]);

                                if (isset($request['data']['b']['commission_split']) && is_array($request['data']['b']['commission_split'])) {
                                    foreach ($request['data']['b']['commission_split'] as $intId => $comRow) {
                                        PolicyFeeSummaryCommissionSplit::updateOrCreate([
                                            'policy_id' => $policy->id,
                                            'policy_fee_summary_internal_fee_id' => $keyRoles1->id,
                                            'policy_introducer_id' => $intId,
                                        ],[
                                            'policy_id' => $policy->id,
                                            'policy_fee_summary_internal_fee_id' => $keyRoles1->id,
                                            'policy_introducer_id' => $intId,
                                            'commission' => $comRow
                                        ]);
                                    }
                                }
                            } else {
                                $keyRoles1 = PolicyFeeSummaryInternalFee::create([
                                    'policy_fee_summary_internal_id' => $created->id,
                                    'type' => $request['data']['b']['type'],
                                    'frequency' => $request['data']['b']['frequency'] ?? '',
                                    'amount' => $request['data']['b']['amount'] ?? 0,
                                    'rate' => $request['data']['b']['rate'] ?? 0,
                                    'notes' => $request['data']['b']['note'] ?? '',
                                    'added_by' => $currentLoggedInUser
                                ]);

                                if (isset($request['data']['b']['commission_split']) && is_array($request['data']['b']['commission_split'])) {
                                    foreach ($request['data']['b']['commission_split'] as $intId => $comRow) {
                                        PolicyFeeSummaryCommissionSplit::updateOrCreate([
                                            'policy_id' => $policy->id,
                                            'policy_fee_summary_internal_fee_id' => $keyRoles1->id,
                                            'policy_introducer_id' => $intId,
                                        ],[
                                            'policy_id' => $policy->id,
                                            'policy_fee_summary_internal_fee_id' => $keyRoles1->id,
                                            'policy_introducer_id' => $intId,
                                            'commission' => $comRow
                                        ]);
                                    }
                                }
                            }
                        }

                        if ($request->has('data.c')) {
                            $coiSelected = $request['data']['fee_option']['coi'] ?? null;
                            if ($coiSelected === 'Other (specify)') {
                                $coiSelected = $request['data']['fee_option_other']['coi'] ?? $coiSelected;
                            }
                            $keyRoles1 = PolicyFeeSummaryInternalFee::where('policy_fee_summary_internal_id', $created->id)->where('type', $request['data']['c']['type'])->first();
                            if ($keyRoles1) {
                                PolicyFeeSummaryInternalFee::where('policy_fee_summary_internal_id', $created->id)->where('type', $request['data']['c']['type'])->update([
                                    'type' => $request['data']['c']['type'],
                                    'frequency' => $request['data']['c']['frequency'] ?? '',
                                    'amount' => $request['data']['c']['amount'] ?? 0,
                                    'rate' => $request['data']['c']['rate'] ?? 0,
                                    'notes' => $request['data']['c']['note'] ?? '',
                                    'fee_option' => $coiSelected,
                                    'updated_by' => $currentLoggedInUser
                                ]);

                                if (isset($request['data']['c']['commission_split']) && is_array($request['data']['c']['commission_split'])) {
                                    foreach ($request['data']['c']['commission_split'] as $intId => $comRow) {
                                        PolicyFeeSummaryCommissionSplit::updateOrCreate([
                                            'policy_id' => $policy->id,
                                            'policy_fee_summary_internal_fee_id' => $keyRoles1->id,
                                            'policy_introducer_id' => $intId,
                                        ],[
                                            'policy_id' => $policy->id,
                                            'policy_fee_summary_internal_fee_id' => $keyRoles1->id,
                                            'policy_introducer_id' => $intId,
                                            'commission' => $comRow
                                        ]);
                                    }
                                }
                            } else {
                                $keyRoles1 = PolicyFeeSummaryInternalFee::create([
                                    'policy_fee_summary_internal_id' => $created->id,
                                    'type' => $request['data']['c']['type'],
                                    'frequency' => $request['data']['c']['frequency'] ?? '',
                                    'amount' => $request['data']['c']['amount'] ?? 0,
                                    'rate' => $request['data']['c']['rate'] ?? 0,
                                    'notes' => $request['data']['c']['note'] ?? '',
                                    'fee_option' => $coiSelected,
                                    'added_by' => $currentLoggedInUser
                                ]);

                                if (isset($request['data']['c']['commission_split']) && is_array($request['data']['c']['commission_split'])) {
                                    foreach ($request['data']['c']['commission_split'] as $intId => $comRow) {
                                        PolicyFeeSummaryCommissionSplit::updateOrCreate([
                                            'policy_id' => $policy->id,
                                            'policy_fee_summary_internal_fee_id' => $keyRoles1->id,
                                            'policy_introducer_id' => $intId,
                                        ],[
                                            'policy_id' => $policy->id,
                                            'policy_fee_summary_internal_fee_id' => $keyRoles1->id,
                                            'policy_introducer_id' => $intId,
                                            'commission' => $comRow
                                        ]);
                                    }
                                }
                            }
                        }

                        if ($request->has('data.f')) {
                            $keyRoles1 = PolicyFeeSummaryInternalFee::where('policy_fee_summary_internal_id', $created->id)->where('type', $request['data']['d']['type'])->first();
                            if ($keyRoles1) {
                                PolicyFeeSummaryInternalFee::where('policy_fee_summary_internal_id', $created->id)->where('type', $request['data']['d']['type'])->update([
                                    'type' => $request['data']['d']['type'],
                                    'frequency' => $request['data']['d']['frequency'] ?? '',
                                    'amount' => $request['data']['d']['amount'] ?? 0,
                                    'rate' => $request['data']['d']['rate'] ?? 0,
                                    'notes' => $request['data']['d']['note'] ?? '',
                                    'updated_by' => $currentLoggedInUser
                                ]);

                                if (isset($request['data']['d']['commission_split']) && is_array($request['data']['d']['commission_split'])) {
                                    foreach ($request['data']['d']['commission_split'] as $intId => $comRow) {
                                        PolicyFeeSummaryCommissionSplit::updateOrCreate([
                                            'policy_id' => $policy->id,
                                            'policy_fee_summary_internal_fee_id' => $keyRoles1->id,
                                            'policy_introducer_id' => $intId,
                                        ],[
                                            'policy_id' => $policy->id,
                                            'policy_fee_summary_internal_fee_id' => $keyRoles1->id,
                                            'policy_introducer_id' => $intId,
                                            'commission' => $comRow
                                        ]);
                                    }
                                }
                            } else {
                                $keyRoles1 = PolicyFeeSummaryInternalFee::create([
                                    'policy_fee_summary_internal_id' => $created->id,
                                    'type' => $request['data']['d']['type'],
                                    'frequency' => $request['data']['d']['frequency'] ?? '',
                                    'amount' => $request['data']['d']['amount'] ?? 0,
                                    'rate' => $request['data']['d']['rate'] ?? 0,
                                    'notes' => $request['data']['d']['note'] ?? '',
                                    'added_by' => $currentLoggedInUser
                                ]);

                                if (isset($request['data']['d']['commission_split']) && is_array($request['data']['d']['commission_split'])) {
                                    foreach ($request['data']['d']['commission_split'] as $intId => $comRow) {
                                        PolicyFeeSummaryCommissionSplit::updateOrCreate([
                                            'policy_id' => $policy->id,
                                            'policy_fee_summary_internal_fee_id' => $keyRoles1->id,
                                            'policy_introducer_id' => $intId,
                                        ],[
                                            'policy_id' => $policy->id,
                                            'policy_fee_summary_internal_fee_id' => $keyRoles1->id,
                                            'policy_introducer_id' => $intId,
                                            'commission' => $comRow
                                        ]);
                                    }
                                }
                            }
                        }

                        if ($request->has('data.e')) {
                            $keyRoles1 = PolicyFeeSummaryInternalFee::where('policy_fee_summary_internal_id', $created->id)->where('type', $request['data']['e']['type'])->first();
                            if ($keyRoles1) {
                                PolicyFeeSummaryInternalFee::where('policy_fee_summary_internal_id', $created->id)->where('type', $request['data']['e']['type'])->update([
                                    'type' => $request['data']['e']['type'],
                                    'frequency' => $request['data']['e']['frequency'] ?? '',
                                    'amount' => $request['data']['e']['amount'] ?? 0,
                                    'rate' => $request['data']['e']['rate'] ?? 0,
                                    'notes' => $request['data']['e']['note'] ?? '',
                                    'updated_by' => $currentLoggedInUser
                                ]);

                                if (isset($request['data']['e']['commission_split']) && is_array($request['data']['e']['commission_split'])) {
                                    foreach ($request['data']['e']['commission_split'] as $intId => $comRow) {
                                        PolicyFeeSummaryCommissionSplit::updateOrCreate([
                                            'policy_id' => $policy->id,
                                            'policy_fee_summary_internal_fee_id' => $keyRoles1->id,
                                            'policy_introducer_id' => $intId,
                                        ],[
                                            'policy_id' => $policy->id,
                                            'policy_fee_summary_internal_fee_id' => $keyRoles1->id,
                                            'policy_introducer_id' => $intId,
                                            'commission' => $comRow
                                        ]);
                                    }
                                }
                            } else {
                                $keyRoles1 = PolicyFeeSummaryInternalFee::create([
                                    'policy_fee_summary_internal_id' => $created->id,
                                    'type' => $request['data']['e']['type'],
                                    'frequency' => $request['data']['e']['frequency'] ?? '',
                                    'amount' => $request['data']['e']['amount'] ?? 0,
                                    'rate' => $request['data']['e']['rate'] ?? 0,
                                    'notes' => $request['data']['e']['note'] ?? '',
                                    'added_by' => $currentLoggedInUser
                                ]);

                                if (isset($request['data']['e']['commission_split']) && is_array($request['data']['e']['commission_split'])) {
                                    foreach ($request['data']['e']['commission_split'] as $intId => $comRow) {
                                        PolicyFeeSummaryCommissionSplit::updateOrCreate([
                                            'policy_id' => $policy->id,
                                            'policy_fee_summary_internal_fee_id' => $keyRoles1->id,
                                            'policy_introducer_id' => $intId,
                                        ],[
                                            'policy_id' => $policy->id,
                                            'policy_fee_summary_internal_fee_id' => $keyRoles1->id,
                                            'policy_introducer_id' => $intId,
                                            'commission' => $comRow
                                        ]);
                                    }
                                }
                            }
                        }

                        if ($request->has('data.f')) {
                            $keyRoles1 = PolicyFeeSummaryInternalFee::where('policy_fee_summary_internal_id', $created->id)->where('type', $request['data']['f']['type'])->first();
                            if ($keyRoles1) {
                                PolicyFeeSummaryInternalFee::where('policy_fee_summary_internal_id', $created->id)->where('type', $request['data']['f']['type'])->update([
                                    'type' => $request['data']['f']['type'],
                                    'frequency' => $request['data']['f']['frequency'] ?? '',
                                    'amount' => $request['data']['f']['amount'] ?? 0,
                                    'rate' => $request['data']['f']['rate'] ?? 0,
                                    'notes' => $request['data']['f']['note'] ?? '',
                                    'updated_by' => $currentLoggedInUser
                                ]);

                                if (isset($request['data']['f']['commission_split']) && is_array($request['data']['f']['commission_split'])) {
                                    foreach ($request['data']['f']['commission_split'] as $intId => $comRow) {
                                        PolicyFeeSummaryCommissionSplit::updateOrCreate([
                                            'policy_id' => $policy->id,
                                            'policy_fee_summary_internal_fee_id' => $keyRoles1->id,
                                            'policy_introducer_id' => $intId,
                                        ],[
                                            'policy_id' => $policy->id,
                                            'policy_fee_summary_internal_fee_id' => $keyRoles1->id,
                                            'policy_introducer_id' => $intId,
                                            'commission' => $comRow
                                        ]);
                                    }
                                }
                            } else {
                                $keyRoles1 = PolicyFeeSummaryInternalFee::create([
                                    'policy_fee_summary_internal_id' => $created->id,
                                    'type' => $request['data']['f']['type'],
                                    'frequency' => $request['data']['f']['frequency'] ?? '',
                                    'amount' => $request['data']['f']['amount'] ?? 0,
                                    'rate' => $request['data']['f']['rate'] ?? 0,
                                    'notes' => $request['data']['f']['note'] ?? '',
                                    'added_by' => $currentLoggedInUser
                                ]);

                                if (isset($request['data']['f']['commission_split']) && is_array($request['data']['f']['commission_split'])) {
                                    foreach ($request['data']['f']['commission_split'] as $intId => $comRow) {
                                        PolicyFeeSummaryCommissionSplit::updateOrCreate([
                                            'policy_id' => $policy->id,
                                            'policy_fee_summary_internal_fee_id' => $keyRoles1->id,
                                            'policy_introducer_id' => $intId,
                                        ],[
                                            'policy_id' => $policy->id,
                                            'policy_fee_summary_internal_fee_id' => $keyRoles1->id,
                                            'policy_introducer_id' => $intId,
                                            'commission' => $comRow
                                        ]);
                                    }
                                }
                            }
                        }

                        if ($request->has('data.g')) {
                            $keyRoles1 = PolicyFeeSummaryInternalFee::where('policy_fee_summary_internal_id', $created->id)->where('type', $request['data']['g']['type'])->first();
                            if ($keyRoles1) {
                                PolicyFeeSummaryInternalFee::where('policy_fee_summary_internal_id', $created->id)->where('type', $request['data']['g']['type'])->update([
                                    'type' => $request['data']['g']['type'],
                                    'frequency' => $request['data']['g']['frequency'] ?? '',
                                    'amount' => $request['data']['g']['amount'] ?? 0,
                                    'rate' => $request['data']['g']['rate'] ?? 0,
                                    'notes' => $request['data']['g']['note'] ?? '',
                                    'updated_by' => $currentLoggedInUser
                                ]);

                                if (isset($request['data']['g']['commission_split']) && is_array($request['data']['g']['commission_split'])) {
                                    foreach ($request['data']['g']['commission_split'] as $intId => $comRow) {
                                        PolicyFeeSummaryCommissionSplit::updateOrCreate([
                                            'policy_id' => $policy->id,
                                            'policy_fee_summary_internal_fee_id' => $keyRoles1->id,
                                            'policy_introducer_id' => $intId,
                                        ],[
                                            'policy_id' => $policy->id,
                                            'policy_fee_summary_internal_fee_id' => $keyRoles1->id,
                                            'policy_introducer_id' => $intId,
                                            'commission' => $comRow
                                        ]);
                                    }
                                }
                            } else {
                                $keyRoles1 = PolicyFeeSummaryInternalFee::create([
                                    'policy_fee_summary_internal_id' => $created->id,
                                    'type' => $request['data']['g']['type'],
                                    'frequency' => $request['data']['g']['frequency'] ?? '',
                                    'amount' => $request['data']['g']['amount'] ?? 0,
                                    'rate' => $request['data']['g']['rate'] ?? 0,
                                    'notes' => $request['data']['g']['note'] ?? '',
                                    'added_by' => $currentLoggedInUser
                                ]);

                                if (isset($request['data']['g']['commission_split']) && is_array($request['data']['g']['commission_split'])) {
                                    foreach ($request['data']['g']['commission_split'] as $intId => $comRow) {
                                        PolicyFeeSummaryCommissionSplit::updateOrCreate([
                                            'policy_id' => $policy->id,
                                            'policy_fee_summary_internal_fee_id' => $keyRoles1->id,
                                            'policy_introducer_id' => $intId,
                                        ],[
                                            'policy_id' => $policy->id,
                                            'policy_fee_summary_internal_fee_id' => $keyRoles1->id,
                                            'policy_introducer_id' => $intId,
                                            'commission' => $comRow
                                        ]);
                                    }
                                }
                            }
                        }
                    }

                    $response['next_section'] = self::$sections[13];
                    return $response;
                case self::$sections[13]:

                    if ($request->has('data.a')) {
                        $keyRoles1 = PolicyFeeSummaryExternal::where('policy_id', $policy->id)->where('type', $request['data']['a']['type'])->first();
                        if ($keyRoles1) {
                            PolicyFeeSummaryExternal::where('policy_id', $policy->id)->where('type', $request['data']['a']['type'])->update([
                                'type' => $request['data']['a']['type'],
                                'frequency' => $request['data']['a']['frequency'] ?? '',
                                'amount' => $request['data']['a']['amount'] ?? 0,
                                'rate' => $request['data']['a']['rate'] ?? 0,
                                'recipient' => $request['data']['a']['recipient'] ?? '',
                                'notes' => $request['data']['a']['note'] ?? '',
                                'updated_by' => $currentLoggedInUser
                            ]);
                        } else {
                            PolicyFeeSummaryExternal::create([
                                'policy_id' => $policy->id,
                                'type' => $request['data']['a']['type'],
                                'frequency' => $request['data']['a']['frequency'] ?? '',
                                'amount' => $request['data']['a']['amount'] ?? 0,
                                'rate' => $request['data']['a']['rate'] ?? 0,
                                'recipient' => $request['data']['a']['recipient'] ?? '',
                                'notes' => $request['data']['a']['note'] ?? '',
                                'added_by' => $currentLoggedInUser
                            ]);
                        }
                    }

                    if ($request->has('data.b')) {
                        $keyRoles1 = PolicyFeeSummaryExternal::where('policy_id', $policy->id)->where('type', $request['data']['b']['type'])->first();
                        if ($keyRoles1) {
                            PolicyFeeSummaryExternal::where('policy_id', $policy->id)->where('type', $request['data']['b']['type'])->update([
                                'type' => $request['data']['b']['type'],
                                'frequency' => $request['data']['b']['frequency'] ?? '',
                                'amount' => $request['data']['b']['amount'] ?? 0,
                                'rate' => $request['data']['b']['rate'] ?? 0,
                                'recipient' => $request['data']['b']['recipient'] ?? '',
                                'notes' => $request['data']['b']['note'] ?? '',
                                'updated_by' => $currentLoggedInUser
                            ]);
                        } else {
                            PolicyFeeSummaryExternal::create([
                                'policy_id' => $policy->id,
                                'type' => $request['data']['b']['type'],
                                'frequency' => $request['data']['b']['frequency'] ?? '',
                                'amount' => $request['data']['b']['amount'] ?? 0,
                                'rate' => $request['data']['b']['rate'] ?? 0,
                                'recipient' => $request['data']['b']['recipient'] ?? '',
                                'notes' => $request['data']['b']['note'] ?? '',
                                'added_by' => $currentLoggedInUser
                            ]);
                        }
                    }
                    
                    if ($request->has('data.c')) {
                        $keyRoles1 = PolicyFeeSummaryExternal::where('policy_id', $policy->id)->where('type', $request['data']['c']['type'])->first();
                        if ($keyRoles1) {
                            PolicyFeeSummaryExternal::where('policy_id', $policy->id)->where('type', $request['data']['c']['type'])->update([
                                'type' => $request['data']['c']['type'],
                                'frequency' => $request['data']['c']['frequency'] ?? '',
                                'amount' => $request['data']['c']['amount'] ?? 0,
                                'rate' => $request['data']['c']['rate'] ?? 0,
                                'recipient' => $request['data']['c']['recipient'] ?? '',
                                'notes' => $request['data']['c']['note'] ?? '',
                                'updated_by' => $currentLoggedInUser
                            ]);
                        } else {
                            PolicyFeeSummaryExternal::create([
                                'policy_id' => $policy->id,
                                'type' => $request['data']['c']['type'],
                                'frequency' => $request['data']['c']['frequency'] ?? '',
                                'amount' => $request['data']['c']['amount'] ?? 0,
                                'rate' => $request['data']['c']['rate'] ?? 0,
                                'recipient' => $request['data']['c']['recipient'] ?? '',
                                'notes' => $request['data']['c']['note'] ?? '',
                                'added_by' => $currentLoggedInUser
                            ]);
                        }
                    }
                    
                    if ($request->has('data.d')) {
                        $keyRoles1 = PolicyFeeSummaryExternal::where('policy_id', $policy->id)->where('type', $request['data']['d']['type'])->first();
                        if ($keyRoles1) {
                            PolicyFeeSummaryExternal::where('policy_id', $policy->id)->where('type', $request['data']['d']['type'])->update([
                                'type' => $request['data']['d']['type'],
                                'frequency' => $request['data']['d']['frequency'] ?? '',
                                'amount' => $request['data']['d']['amount'] ?? 0,
                                'rate' => $request['data']['d']['rate'] ?? 0,
                                'recipient' => $request['data']['d']['recipient'] ?? '',
                                'notes' => $request['data']['d']['note'] ?? '',
                                'updated_by' => $currentLoggedInUser
                            ]);
                        } else {
                            PolicyFeeSummaryExternal::create([
                                'policy_id' => $policy->id,
                                'type' => $request['data']['d']['type'],
                                'frequency' => $request['data']['d']['frequency'] ?? '',
                                'amount' => $request['data']['d']['amount'] ?? 0,
                                'rate' => $request['data']['d']['rate'] ?? 0,
                                'recipient' => $request['data']['d']['recipient'] ?? '',
                                'notes' => $request['data']['d']['note'] ?? '',
                                'added_by' => $currentLoggedInUser
                            ]);
                        }
                    }
                    
                    if ($request->has('data.e')) {
                        $keyRoles1 = PolicyFeeSummaryExternal::where('policy_id', $policy->id)->where('type', $request['data']['e']['type'])->first();
                        if ($keyRoles1) {
                            PolicyFeeSummaryExternal::where('policy_id', $policy->id)->where('type', $request['data']['e']['type'])->update([
                                'type' => $request['data']['e']['type'],
                                'frequency' => $request['data']['e']['frequency'] ?? '',
                                'amount' => $request['data']['e']['amount'] ?? 0,
                                'rate' => $request['data']['e']['rate'] ?? 0,
                                'recipient' => $request['data']['e']['recipient'] ?? '',
                                'notes' => $request['data']['e']['note'] ?? '',
                                'updated_by' => $currentLoggedInUser
                            ]);
                        } else {
                            PolicyFeeSummaryExternal::create([
                                'policy_id' => $policy->id,
                                'type' => $request['data']['e']['type'],
                                'frequency' => $request['data']['e']['frequency'] ?? '',
                                'amount' => $request['data']['e']['amount'] ?? 0,
                                'rate' => $request['data']['e']['rate'] ?? 0,
                                'recipient' => $request['data']['e']['recipient'] ?? '',
                                'notes' => $request['data']['e']['note'] ?? '',
                                'added_by' => $currentLoggedInUser
                            ]);
                        }
                    }
                    
                    if ($request->has('data.f')) {
                        $keyRoles1 = PolicyFeeSummaryExternal::where('policy_id', $policy->id)->where('type', $request['data']['f']['type'])->first();
                        if ($keyRoles1) {
                            PolicyFeeSummaryExternal::where('policy_id', $policy->id)->where('type', $request['data']['f']['type'])->update([
                                'type' => $request['data']['f']['type'],
                                'frequency' => $request['data']['f']['frequency'] ?? '',
                                'amount' => $request['data']['f']['amount'] ?? 0,
                                'rate' => $request['data']['f']['rate'] ?? 0,
                                'recipient' => $request['data']['f']['recipient'] ?? '',
                                'notes' => $request['data']['f']['note'] ?? '',
                                'updated_by' => $currentLoggedInUser
                            ]);
                        } else {
                            PolicyFeeSummaryExternal::create([
                                'policy_id' => $policy->id,
                                'type' => $request['data']['f']['type'],
                                'frequency' => $request['data']['f']['frequency'] ?? '',
                                'amount' => $request['data']['f']['amount'] ?? 0,
                                'rate' => $request['data']['f']['rate'] ?? 0,
                                'recipient' => $request['data']['f']['recipient'] ?? '',
                                'notes' => $request['data']['f']['note'] ?? '',
                                'added_by' => $currentLoggedInUser
                            ]);
                        }
                    }

                    if ($request->has('data.g')) {
                        $keyRoles1 = PolicyFeeSummaryExternal::where('policy_id', $policy->id)->where('type', $request['data']['g']['type'])->first();
                        if ($keyRoles1) {
                            PolicyFeeSummaryExternal::where('policy_id', $policy->id)->where('type', $request['data']['g']['type'])->update([
                                'type' => $request['data']['g']['type'],
                                'frequency' => $request['data']['g']['frequency'] ?? '',
                                'amount' => $request['data']['g']['amount'] ?? 0,
                                'rate' => $request['data']['g']['rate'] ?? 0,
                                'recipient' => $request['data']['g']['recipient'] ?? '',
                                'notes' => $request['data']['g']['note'] ?? '',
                                'updated_by' => $currentLoggedInUser
                            ]);
                        } else {
                            PolicyFeeSummaryExternal::create([
                                'policy_id' => $policy->id,
                                'type' => $request['data']['g']['type'],
                                'frequency' => $request['data']['g']['frequency'] ?? '',
                                'amount' => $request['data']['g']['amount'] ?? 0,
                                'rate' => $request['data']['g']['rate'] ?? 0,
                                'recipient' => $request['data']['g']['recipient'] ?? '',
                                'notes' => $request['data']['g']['note'] ?? '',
                                'added_by' => $currentLoggedInUser
                            ]);
                        }
                    }

                    $response['next_section'] = self::$sections[14];
                    return $response;
                case self::$sections[14]:

                    if ($request->has('data.a')) {
                        $keyRoles1 = PolicyInception::where('policy_id', $policy->id)->where('asset_class', $request['data']['a']['asset_class'])->first();
                        if ($keyRoles1) {
                            PolicyInception::where('policy_id', $policy->id)->where('asset_class', $request['data']['a']['asset_class'])->update([
                                'asset_class' => $request['data']['a']['asset_class'],
                                'included' => isset($request['data']['a']['included']) && $request['data']['a']['included'] == 'yes' ? 'yes' : 'no',
                                'est_of_portfolio' => $request['data']['a']['est'] ?? '',
                                'valuation_support' => $request['data']['a']['val'] ?? '',
                                'notes' => $request['data']['a']['note'] ?? '',
                                'updated_by' => $currentLoggedInUser
                            ]);
                        } else {
                            PolicyInception::create([
                                'asset_class' => $request['data']['a']['asset_class'],
                                'policy_id' => $policy->id,
                                'included' => isset($request['data']['a']['included']) && $request['data']['a']['included'] == 'yes' ? 'yes' : 'no',
                                'est_of_portfolio' => $request['data']['a']['est'] ?? '',
                                'valuation_support' => $request['data']['a']['val'] ?? '',
                                'notes' => $request['data']['a']['note'] ?? '',
                                'added_by' => $currentLoggedInUser
                            ]);
                        }
                    }

                    if ($request->has('data.b')) {
                        $keyRoles1 = PolicyInception::where('policy_id', $policy->id)->where('asset_class', $request['data']['b']['asset_class'])->first();
                        if ($keyRoles1) {
                            PolicyInception::where('policy_id', $policy->id)->where('asset_class', $request['data']['b']['asset_class'])->update([
                                'asset_class' => $request['data']['b']['asset_class'],
                                'included' => isset($request['data']['b']['included']) && $request['data']['b']['included'] == 'yes' ? 'yes' : 'no',
                                'est_of_portfolio' => $request['data']['b']['est'] ?? '',
                                'valuation_support' => $request['data']['b']['val'] ?? '',
                                'notes' => $request['data']['b']['note'] ?? '',
                                'updated_by' => $currentLoggedInUser
                            ]);
                        } else {
                            PolicyInception::create([
                                'asset_class' => $request['data']['b']['asset_class'],
                                'policy_id' => $policy->id,
                                'included' => isset($request['data']['b']['included']) && $request['data']['b']['included'] == 'yes' ? 'yes' : 'no',
                                'est_of_portfolio' => $request['data']['b']['est'] ?? '',
                                'valuation_support' => $request['data']['b']['val'] ?? '',
                                'notes' => $request['data']['b']['note'] ?? '',
                                'added_by' => $currentLoggedInUser
                            ]);
                        }
                    }
                    
                    if ($request->has('data.c')) {
                        $keyRoles1 = PolicyInception::where('policy_id', $policy->id)->where('asset_class', $request['data']['c']['asset_class'])->first();
                        if ($keyRoles1) {
                            PolicyInception::where('policy_id', $policy->id)->where('asset_class', $request['data']['c']['asset_class'])->update([
                                'asset_class' => $request['data']['c']['asset_class'],
                                'included' => isset($request['data']['c']['included']) && $request['data']['c']['included'] == 'yes' ? 'yes' : 'no',
                                'est_of_portfolio' => $request['data']['c']['est'] ?? '',
                                'valuation_support' => $request['data']['c']['val'] ?? '',
                                'notes' => $request['data']['c']['note'] ?? '',
                                'updated_by' => $currentLoggedInUser
                            ]);
                        } else {
                            PolicyInception::create([
                                'asset_class' => $request['data']['c']['asset_class'],
                                'policy_id' => $policy->id,
                                'included' => isset($request['data']['c']['included']) && $request['data']['c']['included'] == 'yes' ? 'yes' : 'no',
                                'est_of_portfolio' => $request['data']['c']['est'] ?? '',
                                'valuation_support' => $request['data']['c']['val'] ?? '',
                                'notes' => $request['data']['c']['note'] ?? '',
                                'added_by' => $currentLoggedInUser
                            ]);
                        }
                    }
                    
                    if ($request->has('data.d')) {
                        $keyRoles1 = PolicyInception::where('policy_id', $policy->id)->where('asset_class', $request['data']['d']['asset_class'])->first();
                        if ($keyRoles1) {
                            PolicyInception::where('policy_id', $policy->id)->where('asset_class', $request['data']['d']['asset_class'])->update([
                                'asset_class' => $request['data']['d']['asset_class'],
                                'included' => isset($request['data']['d']['included']) && $request['data']['d']['included'] == 'yes' ? 'yes' : 'no',
                                'est_of_portfolio' => $request['data']['d']['est'] ?? '',
                                'valuation_support' => $request['data']['d']['val'] ?? '',
                                'notes' => $request['data']['d']['note'] ?? '',
                                'updated_by' => $currentLoggedInUser
                            ]);
                        } else {
                            PolicyInception::create([
                                'asset_class' => $request['data']['d']['asset_class'],
                                'policy_id' => $policy->id,
                                'included' => isset($request['data']['d']['included']) && $request['data']['d']['included'] == 'yes' ? 'yes' : 'no',
                                'est_of_portfolio' => $request['data']['d']['est'] ?? '',
                                'valuation_support' => $request['data']['d']['val'] ?? '',
                                'notes' => $request['data']['d']['note'] ?? '',
                                'added_by' => $currentLoggedInUser
                            ]);
                        }
                    }
                    
                    if ($request->has('data.e')) {
                        $keyRoles1 = PolicyInception::where('policy_id', $policy->id)->where('asset_class', $request['data']['e']['asset_class'])->first();
                        if ($keyRoles1) {
                            PolicyInception::where('policy_id', $policy->id)->where('asset_class', $request['data']['e']['asset_class'])->update([
                                'asset_class' => $request['data']['e']['asset_class'],
                                'included' => isset($request['data']['e']['included']) && $request['data']['e']['included'] == 'yes' ? 'yes' : 'no',
                                'est_of_portfolio' => $request['data']['e']['est'] ?? '',
                                'valuation_support' => $request['data']['e']['val'] ?? '',
                                'notes' => $request['data']['e']['note'] ?? '',
                                'updated_by' => $currentLoggedInUser
                            ]);
                        } else {
                            PolicyInception::create([
                                'asset_class' => $request['data']['e']['asset_class'],
                                'policy_id' => $policy->id,
                                'included' => isset($request['data']['e']['included']) && $request['data']['e']['included'] == 'yes' ? 'yes' : 'no',
                                'est_of_portfolio' => $request['data']['e']['est'] ?? '',
                                'valuation_support' => $request['data']['e']['val'] ?? '',
                                'notes' => $request['data']['e']['note'] ?? '',
                                'added_by' => $currentLoggedInUser
                            ]);
                        }
                    }
                    
                    if ($request->has('data.f')) {
                        $keyRoles1 = PolicyInception::where('policy_id', $policy->id)->where('asset_class', $request['data']['f']['asset_class'])->first();
                        if ($keyRoles1) {
                            PolicyInception::where('policy_id', $policy->id)->where('asset_class', $request['data']['f']['asset_class'])->update([
                                'asset_class' => $request['data']['f']['asset_class'],
                                'included' => isset($request['data']['f']['included']) && $request['data']['f']['included'] == 'yes' ? 'yes' : 'no',
                                'est_of_portfolio' => $request['data']['f']['est'] ?? '',
                                'valuation_support' => $request['data']['f']['val'] ?? '',
                                'notes' => $request['data']['f']['note'] ?? '',
                                'updated_by' => $currentLoggedInUser
                            ]);
                        } else {
                            PolicyInception::create([
                                'asset_class' => $request['data']['f']['asset_class'],
                                'policy_id' => $policy->id,
                                'included' => isset($request['data']['f']['included']) && $request['data']['f']['included'] == 'yes' ? 'yes' : 'no',
                                'est_of_portfolio' => $request['data']['f']['est'] ?? '',
                                'valuation_support' => $request['data']['f']['val'] ?? '',
                                'notes' => $request['data']['f']['note'] ?? '',
                                'added_by' => $currentLoggedInUser
                            ]);
                        }
                    }
                    
                    if ($request->has('data.g')) {
                        $keyRoles1 = PolicyInception::where('policy_id', $policy->id)->where('asset_class', $request['data']['g']['asset_class'])->first();
                        if ($keyRoles1) {
                            PolicyInception::where('policy_id', $policy->id)->where('asset_class', $request['data']['g']['asset_class'])->update([
                                'asset_class' => $request['data']['g']['asset_class'],
                                'included' => isset($request['data']['g']['included']) && $request['data']['a']['included'] == 'yes' ? 'yes' : 'no',
                                'est_of_portfolio' => $request['data']['g']['est'] ?? '',
                                'valuation_support' => $request['data']['g']['val'] ?? '',
                                'notes' => $request['data']['g']['note'] ?? '',
                                'updated_by' => $currentLoggedInUser
                            ]);
                        } else {
                            PolicyInception::create([
                                'asset_class' => $request['data']['g']['asset_class'],
                                'policy_id' => $policy->id,
                                'included' => isset($request['data']['g']['included']) && $request['data']['g']['included'] == 'yes' ? 'yes' : 'no',
                                'est_of_portfolio' => $request['data']['g']['est'] ?? '',
                                'valuation_support' => $request['data']['g']['val'] ?? '',
                                'notes' => $request['data']['g']['note'] ?? '',
                                'added_by' => $currentLoggedInUser
                            ]);
                        }
                    }

                    if ($request->has('data.h')) {
                        $keyRoles1 = PolicyInception::where('policy_id', $policy->id)->where('asset_class', $request['data']['h']['asset_class'])->first();
                        if ($keyRoles1) {
                            PolicyInception::where('policy_id', $policy->id)->where('asset_class', $request['data']['h']['asset_class'])->update([
                                'asset_class' => $request['data']['h']['asset_class'],
                                'included' => isset($request['data']['h']['included']) && $request['data']['h']['included'] == 'yes' ? 'yes' : 'no',
                                'est_of_portfolio' => $request['data']['h']['est'] ?? '',
                                'valuation_support' => $request['data']['h']['val'] ?? '',
                                'notes' => $request['data']['h']['note'] ?? '',
                                'updated_by' => $currentLoggedInUser
                            ]);
                        } else {
                            PolicyInception::create([
                                'asset_class' => $request['data']['h']['asset_class'],
                                'policy_id' => $policy->id,
                                'included' => isset($request['data']['h']['included']) && $request['data']['h']['included'] == 'yes' ? 'yes' : 'no',
                                'est_of_portfolio' => $request['data']['h']['est'] ?? '',
                                'valuation_support' => $request['data']['h']['val'] ?? '',
                                'notes' => $request['data']['h']['note'] ?? '',
                                'added_by' => $currentLoggedInUser
                            ]);
                        }
                    }

                    if ($request->has('data.i')) {
                        $keyRoles1 = PolicyInception::where('policy_id', $policy->id)->where('asset_class', $request['data']['i']['asset_class'])->first();
                        if ($keyRoles1) {
                            PolicyInception::where('policy_id', $policy->id)->where('asset_class', $request['data']['i']['asset_class'])->update([
                                'asset_class' => $request['data']['i']['asset_class'],
                                'included' => isset($request['data']['i']['included']) && $request['data']['i']['included'] == 'yes' ? 'yes' : 'no',
                                'est_of_portfolio' => $request['data']['i']['est'] ?? '',
                                'valuation_support' => $request['data']['i']['val'] ?? '',
                                'notes' => $request['data']['i']['note'] ?? '',
                                'updated_by' => $currentLoggedInUser
                            ]);
                        } else {
                            PolicyInception::create([
                                'asset_class' => $request['data']['i']['asset_class'],
                                'policy_id' => $policy->id,
                                'included' => isset($request['data']['i']['included']) && $request['data']['i']['included'] == 'yes' ? 'yes' : 'no',
                                'est_of_portfolio' => $request['data']['i']['est'] ?? '',
                                'valuation_support' => $request['data']['i']['val'] ?? '',
                                'notes' => $request['data']['i']['note'] ?? '',
                                'added_by' => $currentLoggedInUser
                            ]);
                        }
                    }

                    if ($request->has('data.j')) {
                        $keyRoles1 = PolicyInception::where('policy_id', $policy->id)->where('asset_class', $request['data']['j']['asset_class'])->first();
                        if ($keyRoles1) {
                            PolicyInception::where('policy_id', $policy->id)->where('asset_class', $request['data']['j']['asset_class'])->update([
                                'asset_class' => $request['data']['j']['asset_class'],
                                'included' => isset($request['data']['j']['included']) && $request['data']['j']['included'] == 'yes' ? 'yes' : 'no',
                                'est_of_portfolio' => $request['data']['j']['est'] ?? '',
                                'valuation_support' => $request['data']['j']['val'] ?? '',
                                'notes' => $request['data']['j']['note'] ?? '',
                                'updated_by' => $currentLoggedInUser
                            ]);
                        } else {
                            PolicyInception::create([
                                'asset_class' => $request['data']['j']['asset_class'],
                                'policy_id' => $policy->id,
                                'included' => isset($request['data']['j']['included']) && $request['data']['j']['included'] == 'yes' ? 'yes' : 'no',
                                'est_of_portfolio' => $request['data']['j']['est'] ?? '',
                                'valuation_support' => $request['data']['j']['val'] ?? '',
                                'notes' => $request['data']['j']['note'] ?? '',
                                'added_by' => $currentLoggedInUser
                            ]);
                        }
                    }

                    $response['next_section'] = self::$sections[15];
                    return $response;
                case self::$sections[15]:

                    if ($request->has('data.a')) {
                        $keyRoles1 = PolicyOnGoing::where('policy_id', $policy->id)->where('asset_class', $request['data']['a']['asset_class'])->first();
                        if ($keyRoles1) {
                            PolicyOnGoing::where('policy_id', $policy->id)->where('asset_class', $request['data']['a']['asset_class'])->update([
                                'asset_class' => $request['data']['a']['asset_class'],
                                'included' => isset($request['data']['a']['included']) && $request['data']['a']['included'] == 'yes' ? 'yes' : 'no',
                                'est_of_portfolio' => $request['data']['a']['est'] ?? '',
                                'valuation_support' => $request['data']['a']['val'] ?? '',
                                'notes' => $request['data']['a']['note'] ?? '',
                                'updated_by' => $currentLoggedInUser
                            ]);
                        } else {
                            PolicyOnGoing::create([
                                'asset_class' => $request['data']['a']['asset_class'],
                                'policy_id' => $policy->id,
                                'included' => isset($request['data']['a']['included']) && $request['data']['a']['included'] == 'yes' ? 'yes' : 'no',
                                'est_of_portfolio' => $request['data']['a']['est'] ?? '',
                                'valuation_support' => $request['data']['a']['val'] ?? '',
                                'notes' => $request['data']['a']['note'] ?? '',
                                'added_by' => $currentLoggedInUser
                            ]);
                        }
                    }

                    if ($request->has('data.b')) {
                        $keyRoles1 = PolicyOnGoing::where('policy_id', $policy->id)->where('asset_class', $request['data']['b']['asset_class'])->first();
                        if ($keyRoles1) {
                            PolicyOnGoing::where('policy_id', $policy->id)->where('asset_class', $request['data']['b']['asset_class'])->update([
                                'asset_class' => $request['data']['b']['asset_class'],
                                'included' => isset($request['data']['b']['included']) && $request['data']['b']['included'] == 'yes' ? 'yes' : 'no',
                                'est_of_portfolio' => $request['data']['b']['est'] ?? '',
                                'valuation_support' => $request['data']['b']['val'] ?? '',
                                'notes' => $request['data']['b']['note'] ?? '',
                                'updated_by' => $currentLoggedInUser
                            ]);
                        } else {
                            PolicyOnGoing::create([
                                'asset_class' => $request['data']['b']['asset_class'],
                                'policy_id' => $policy->id,
                                'included' => isset($request['data']['b']['included']) && $request['data']['b']['included'] == 'yes' ? 'yes' : 'no',
                                'est_of_portfolio' => $request['data']['b']['est'] ?? '',
                                'valuation_support' => $request['data']['b']['val'] ?? '',
                                'notes' => $request['data']['b']['note'] ?? '',
                                'added_by' => $currentLoggedInUser
                            ]);
                        }
                    }
                    
                    if ($request->has('data.c')) {
                        $keyRoles1 = PolicyOnGoing::where('policy_id', $policy->id)->where('asset_class', $request['data']['c']['asset_class'])->first();
                        if ($keyRoles1) {
                            PolicyOnGoing::where('policy_id', $policy->id)->where('asset_class', $request['data']['c']['asset_class'])->update([
                                'asset_class' => $request['data']['c']['asset_class'],
                                'included' => isset($request['data']['c']['included']) && $request['data']['c']['included'] == 'yes' ? 'yes' : 'no',
                                'est_of_portfolio' => $request['data']['c']['est'] ?? '',
                                'valuation_support' => $request['data']['c']['val'] ?? '',
                                'notes' => $request['data']['c']['note'] ?? '',
                                'updated_by' => $currentLoggedInUser
                            ]);
                        } else {
                            PolicyOnGoing::create([
                                'asset_class' => $request['data']['c']['asset_class'],
                                'policy_id' => $policy->id,
                                'included' => isset($request['data']['c']['included']) && $request['data']['c']['included'] == 'yes' ? 'yes' : 'no',
                                'est_of_portfolio' => $request['data']['c']['est'] ?? '',
                                'valuation_support' => $request['data']['c']['val'] ?? '',
                                'notes' => $request['data']['c']['note'] ?? '',
                                'added_by' => $currentLoggedInUser
                            ]);
                        }
                    }
                    
                    if ($request->has('data.d')) {
                        $keyRoles1 = PolicyOnGoing::where('policy_id', $policy->id)->where('asset_class', $request['data']['d']['asset_class'])->first();
                        if ($keyRoles1) {
                            PolicyOnGoing::where('policy_id', $policy->id)->where('asset_class', $request['data']['d']['asset_class'])->update([
                                'asset_class' => $request['data']['d']['asset_class'],
                                'included' => isset($request['data']['d']['included']) && $request['data']['d']['included'] == 'yes' ? 'yes' : 'no',
                                'est_of_portfolio' => $request['data']['d']['est'] ?? '',
                                'valuation_support' => $request['data']['d']['val'] ?? '',
                                'notes' => $request['data']['d']['note'] ?? '',
                                'updated_by' => $currentLoggedInUser
                            ]);
                        } else {
                            PolicyOnGoing::create([
                                'asset_class' => $request['data']['d']['asset_class'],
                                'policy_id' => $policy->id,
                                'included' => isset($request['data']['d']['included']) && $request['data']['d']['included'] == 'yes' ? 'yes' : 'no',
                                'est_of_portfolio' => $request['data']['d']['est'] ?? '',
                                'valuation_support' => $request['data']['d']['val'] ?? '',
                                'notes' => $request['data']['d']['note'] ?? '',
                                'added_by' => $currentLoggedInUser
                            ]);
                        }
                    }
                    
                    if ($request->has('data.e')) {
                        $keyRoles1 = PolicyOnGoing::where('policy_id', $policy->id)->where('asset_class', $request['data']['e']['asset_class'])->first();
                        if ($keyRoles1) {
                            PolicyOnGoing::where('policy_id', $policy->id)->where('asset_class', $request['data']['e']['asset_class'])->update([
                                'asset_class' => $request['data']['e']['asset_class'],
                                'included' => isset($request['data']['e']['included']) && $request['data']['e']['included'] == 'yes' ? 'yes' : 'no',
                                'est_of_portfolio' => $request['data']['e']['est'] ?? '',
                                'valuation_support' => $request['data']['e']['val'] ?? '',
                                'notes' => $request['data']['e']['note'] ?? '',
                                'updated_by' => $currentLoggedInUser
                            ]);
                        } else {
                            PolicyOnGoing::create([
                                'asset_class' => $request['data']['e']['asset_class'],
                                'policy_id' => $policy->id,
                                'included' => isset($request['data']['e']['included']) && $request['data']['e']['included'] == 'yes' ? 'yes' : 'no',
                                'est_of_portfolio' => $request['data']['e']['est'] ?? '',
                                'valuation_support' => $request['data']['e']['val'] ?? '',
                                'notes' => $request['data']['e']['note'] ?? '',
                                'added_by' => $currentLoggedInUser
                            ]);
                        }
                    }
                    
                    if ($request->has('data.f')) {
                        $keyRoles1 = PolicyOnGoing::where('policy_id', $policy->id)->where('asset_class', $request['data']['f']['asset_class'])->first();
                        if ($keyRoles1) {
                            PolicyOnGoing::where('policy_id', $policy->id)->where('asset_class', $request['data']['f']['asset_class'])->update([
                                'asset_class' => $request['data']['f']['asset_class'],
                                'included' => isset($request['data']['f']['included']) && $request['data']['f']['included'] == 'yes' ? 'yes' : 'no',
                                'est_of_portfolio' => $request['data']['f']['est'] ?? '',
                                'valuation_support' => $request['data']['f']['val'] ?? '',
                                'notes' => $request['data']['f']['note'] ?? '',
                                'updated_by' => $currentLoggedInUser
                            ]);
                        } else {
                            PolicyOnGoing::create([
                                'asset_class' => $request['data']['f']['asset_class'],
                                'policy_id' => $policy->id,
                                'included' => isset($request['data']['f']['included']) && $request['data']['f']['included'] == 'yes' ? 'yes' : 'no',
                                'est_of_portfolio' => $request['data']['f']['est'] ?? '',
                                'valuation_support' => $request['data']['f']['val'] ?? '',
                                'notes' => $request['data']['f']['note'] ?? '',
                                'added_by' => $currentLoggedInUser
                            ]);
                        }
                    }
                    
                    if ($request->has('data.g')) {
                        $keyRoles1 = PolicyOnGoing::where('policy_id', $policy->id)->where('asset_class', $request['data']['g']['asset_class'])->first();
                        if ($keyRoles1) {
                            PolicyOnGoing::where('policy_id', $policy->id)->where('asset_class', $request['data']['g']['asset_class'])->update([
                                'asset_class' => $request['data']['g']['asset_class'],
                                'included' => isset($request['data']['g']['included']) && $request['data']['a']['included'] == 'yes' ? 'yes' : 'no',
                                'est_of_portfolio' => $request['data']['g']['est'] ?? '',
                                'valuation_support' => $request['data']['g']['val'] ?? '',
                                'notes' => $request['data']['g']['note'] ?? '',
                                'updated_by' => $currentLoggedInUser
                            ]);
                        } else {
                            PolicyOnGoing::create([
                                'asset_class' => $request['data']['g']['asset_class'],
                                'policy_id' => $policy->id,
                                'included' => isset($request['data']['g']['included']) && $request['data']['g']['included'] == 'yes' ? 'yes' : 'no',
                                'est_of_portfolio' => $request['data']['g']['est'] ?? '',
                                'valuation_support' => $request['data']['g']['val'] ?? '',
                                'notes' => $request['data']['g']['note'] ?? '',
                                'added_by' => $currentLoggedInUser
                            ]);
                        }
                    }

                    if ($request->has('data.h')) {
                        $keyRoles1 = PolicyOnGoing::where('policy_id', $policy->id)->where('asset_class', $request['data']['h']['asset_class'])->first();
                        if ($keyRoles1) {
                            PolicyOnGoing::where('policy_id', $policy->id)->where('asset_class', $request['data']['h']['asset_class'])->update([
                                'asset_class' => $request['data']['h']['asset_class'],
                                'included' => isset($request['data']['h']['included']) && $request['data']['h']['included'] == 'yes' ? 'yes' : 'no',
                                'est_of_portfolio' => $request['data']['h']['est'] ?? '',
                                'valuation_support' => $request['data']['h']['val'] ?? '',
                                'notes' => $request['data']['h']['note'] ?? '',
                                'updated_by' => $currentLoggedInUser
                            ]);
                        } else {
                            PolicyOnGoing::create([
                                'asset_class' => $request['data']['h']['asset_class'],
                                'policy_id' => $policy->id,
                                'included' => isset($request['data']['h']['included']) && $request['data']['h']['included'] == 'yes' ? 'yes' : 'no',
                                'est_of_portfolio' => $request['data']['h']['est'] ?? '',
                                'valuation_support' => $request['data']['h']['val'] ?? '',
                                'notes' => $request['data']['h']['note'] ?? '',
                                'added_by' => $currentLoggedInUser
                            ]);
                        }
                    }

                    if ($request->has('data.i')) {
                        $keyRoles1 = PolicyOnGoing::where('policy_id', $policy->id)->where('asset_class', $request['data']['i']['asset_class'])->first();
                        if ($keyRoles1) {
                            PolicyOnGoing::where('policy_id', $policy->id)->where('asset_class', $request['data']['i']['asset_class'])->update([
                                'asset_class' => $request['data']['i']['asset_class'],
                                'included' => isset($request['data']['i']['included']) && $request['data']['i']['included'] == 'yes' ? 'yes' : 'no',
                                'est_of_portfolio' => $request['data']['i']['est'] ?? '',
                                'valuation_support' => $request['data']['i']['val'] ?? '',
                                'notes' => $request['data']['i']['note'] ?? '',
                                'updated_by' => $currentLoggedInUser
                            ]);
                        } else {
                            PolicyOnGoing::create([
                                'asset_class' => $request['data']['i']['asset_class'],
                                'policy_id' => $policy->id,
                                'included' => isset($request['data']['i']['included']) && $request['data']['i']['included'] == 'yes' ? 'yes' : 'no',
                                'est_of_portfolio' => $request['data']['i']['est'] ?? '',
                                'valuation_support' => $request['data']['i']['val'] ?? '',
                                'notes' => $request['data']['i']['note'] ?? '',
                                'added_by' => $currentLoggedInUser
                            ]);
                        }
                    }

                    if ($request->has('data.j')) {
                        $keyRoles1 = PolicyOnGoing::where('policy_id', $policy->id)->where('asset_class', $request['data']['j']['asset_class'])->first();
                        if ($keyRoles1) {
                            PolicyOnGoing::where('policy_id', $policy->id)->where('asset_class', $request['data']['j']['asset_class'])->update([
                                'asset_class' => $request['data']['j']['asset_class'],
                                'included' => isset($request['data']['j']['included']) && $request['data']['j']['included'] == 'yes' ? 'yes' : 'no',
                                'est_of_portfolio' => $request['data']['j']['est'] ?? '',
                                'valuation_support' => $request['data']['j']['val'] ?? '',
                                'notes' => $request['data']['j']['note'] ?? '',
                                'updated_by' => $currentLoggedInUser
                            ]);
                        } else {
                            PolicyOnGoing::create([
                                'asset_class' => $request['data']['j']['asset_class'],
                                'policy_id' => $policy->id,
                                'included' => isset($request['data']['j']['included']) && $request['data']['j']['included'] == 'yes' ? 'yes' : 'no',
                                'est_of_portfolio' => $request['data']['j']['est'] ?? '',
                                'valuation_support' => $request['data']['j']['val'] ?? '',
                                'notes' => $request['data']['j']['note'] ?? '',
                                'added_by' => $currentLoggedInUser
                            ]);
                        }
                    }

                    $response['next_section'] = self::$sections[16];
                    return $response;
                case self::$sections[16]:

                    if (PolicyInvestmentNote::where('policy_id', $policy->id)->exists()) {
                        PolicyInvestmentNote::where('policy_id', $policy->id)->update([
                            'date_of_change_portfolio' => isset($request['data']['portfolio_change_date']) ? date('Y-m-d H:i:s', strtotime($request['data']['portfolio_change_date'])) : null,
                            'portfolio_change' => $request['data']['portfolio_change'] ?? '',
                            'date_of_change_idf' => isset($request['data']['idf_change_date']) ? date('Y-m-d H:i:s', strtotime($request['data']['idf_change_date'])) : null,
                            'idf_change' => $request['data']['idf_change'] ?? '',
                            'date_of_change_transfer' => isset($request['data']['asset_transfer_date']) ? date('Y-m-d H:i:s', strtotime($request['data']['asset_transfer_date'])) : null,
                            'transfer_change' => $request['data']['asset_transfer_note'] ?? '',
                            'decision' => $request['data']['trustee_decisions'] ?? '',
                            'added_by' => $currentLoggedInUser
                        ]);
                    } else {
                        PolicyInvestmentNote::create([
                            'policy_id' => $policy->id,
                            'date_of_change_portfolio' => isset($request['data']['portfolio_change_date']) ? date('Y-m-d H:i:s', strtotime($request['data']['portfolio_change_date'])) : null,
                            'portfolio_change' => $request['data']['portfolio_change'] ?? '',
                            'date_of_change_idf' => isset($request['data']['idf_change_date']) ? date('Y-m-d H:i:s', strtotime($request['data']['idf_change_date'])) : null,
                            'idf_change' => $request['data']['idf_change'] ?? '',
                            'date_of_change_transfer' => isset($request['data']['asset_transfer_date']) ? date('Y-m-d H:i:s', strtotime($request['data']['asset_transfer_date'])) : null,
                            'transfer_change' => $request['data']['asset_transfer_note'] ?? '',
                            'decision' => $request['data']['trustee_decisions'] ?? '',
                            'added_by' => $currentLoggedInUser
                        ]);
                    }

                    $response['next_section'] = self::$sections[19];
                    return $response;
                case self::$sections[17]:

                    PolicyCommunication::create([
                        'policy_id' => $policy->id,
                        'type' => $request['data']['type'] ?? '',
                        'date' => isset($request['data']['date']) ? date('Y-m-d H:i:s', strtotime($request['data']['date'])) : null,
                        'contact_person_involved' => $request['data']['contact_person'] ?? '',
                        'summary_of_discussion' => $request['data']['discussion'] ?? '',
                        'action_taken_or_next_step' => $request['data']['action_taken'] ?? '',
                        'internal_owners' => $request['data']['internal_owners'] ?? '',
                        'added_by' => $currentLoggedInUser
                    ]);

                    if ($request->save == 'save-and-add') {
                        $response['type'] = 'save-and-add';
                        $response['next_section'] = self::$sections[17];
                    } else {
                        $response['next_section'] = self::$sections[18];
                    }

                    return $response;
                case self::$sections[18]:

                    PolicyCaseFileNote::create([
                        'policy_id' => $policy->id,
                        'date' => isset($request['data']['noted_at']) ? date('Y-m-d H:i:s', strtotime($request['data']['noted_at'])) : null,
                        'noted_by' => $request['data']['noted_by'] ?? '',
                        'notes' => $request['data']['note'] ?? '',
                        'added_by' => $currentLoggedInUser
                    ]);

                    if ($request->save == 'save-and-add') {
                        $response['type'] = 'save-and-add';
                        $response['next_section'] = self::$sections[18];
                    } else {
                        $response['next_section'] = null;
                        $policy->update(['status' => 'ACTIVE']);
                    }

                    return $response;
                case self::$sections[19]:

                    if ($request->action === 'delete' && !empty($request->doc_id)) {
                        $doc = \App\Models\DownloadableDocument::where('policy_id', $policy->id)->where('id', $request->doc_id)->first();
                        if ($doc) {
                            PolicyDocument::where('policy_id', $policy->id)
                                ->where('document_type', 'downloadable-document')
                                ->where('document_id', $doc->id)
                                ->delete();
                            $path = storage_path("app/public/customized-form/{$doc->file}");
                            if (is_file($path)) @unlink($path);
                            $doc->delete();
                        }
                        $response['next_section'] = self::$sections[19];
                        return $response;
                    }

                    $folder = 'customized-form';

                    if (!\Illuminate\Support\Facades\Storage::disk('public')->exists($folder)) {
                        \Illuminate\Support\Facades\Storage::disk('public')->makeDirectory($folder);
                    }

                    $uploadedFile = $request->file('file');
                    $title = $request->title ?? null;

                    if ($uploadedFile instanceof \Illuminate\Http\UploadedFile && !empty($request->doc_id)) {
                        $filename = time().'_'.$uploadedFile->getClientOriginalName();
                        $uploadedFile->move(storage_path("app/public/{$folder}"), $filename);

                        if (file_exists(storage_path("app/public/{$folder}/{$filename}")) && is_file(storage_path("app/public/{$folder}/{$filename}"))) {
                            $doc = \App\Models\DownloadableDocument::where('id', (int)$request->doc_id)->first();
                            if ($doc) {
                                $old = $doc->file ? storage_path("app/public/{$folder}/{$doc->file}") : null;
                                if ($old && is_file($old)) { @unlink($old); }
                                $doc->update(['file' => $filename, 'updated_by' => $currentLoggedInUser]);
                            }
                        }

                        $response['next_section'] = self::$sections[19];
                        return $response;
                    }

                    if ($uploadedFile instanceof \Illuminate\Http\UploadedFile && !empty($title)) {
                        $filename = time().'_'.$uploadedFile->getClientOriginalName();
                        $uploadedFile->move(storage_path("app/public/{$folder}"), $filename);

                        if (file_exists(storage_path("app/public/{$folder}/{$filename}")) && is_file(storage_path("app/public/{$folder}/{$filename}"))) {
                            \App\Models\DownloadableDocument::create([
                                'policy_id' => $policy->id,
                                'title' => $title,
                                'file' => $filename,
                                'added_by' => $currentLoggedInUser
                            ]);
                        }
                    }

                    $response['next_section'] = self::$sections[20];
                    return $response;
                case self::$sections[20]:
                    $folder = 'customized-form';

                    if (!\Illuminate\Support\Facades\Storage::disk('public')->exists($folder)) {
                        \Illuminate\Support\Facades\Storage::disk('public')->makeDirectory($folder);
                    }

                    if (is_array($request->file_form)) {
                        foreach ($request->file_form as $form => $file) {
                            
                            if ($file instanceof \Illuminate\Http\UploadedFile) {
                                $filename = time().'_'.$file->getClientOriginalName();

                                $file->move(storage_path("app/public/{$folder}"), $filename);

                                if (file_exists(storage_path("app/public/{$folder}/{$filename}")) && is_file(storage_path("app/public/{$folder}/{$filename}"))) {
                                    UploadableDocument::updateOrCreate([
                                        'downloadable_document_id' => $policy->id,
                                    ], [
                                        'file' => $filename
                                    ]);
                                }
                            }
                        }
                    }

                    if ($request->save == 'save-and-add') {
                        $response['type'] = 'save-and-add';
                        $response['next_section'] = self::$sections[18];
                    } else {
                        $response['next_section'] = null;
                        $policy->update(['status' => 'ACTIVE']);
                    }

                    return $response;

                default:
                }
            }
        }

        return [
            'errors' => [
                'policy' => [
                    'Curreny policy session is expired! Please try again!'
                ]
            ]
        ];
    }

}