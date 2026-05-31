<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\CaseNote;
use App\Models\Client;
use App\Models\Document;
use App\Models\FinancialPeriod;
use App\Models\LegalCase;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class OnyxLegalSeeder extends Seeder
{
    public function run(): void
    {
        // ── USERS ──────────────────────────────────────────────────────────────

        $admin = User::updateOrCreate(
            ['email' => 'admin@onyxlegal.ug'],
            [
                'name'      => 'Mubaraka Ssekidde',
                'username'  => 'admin',
                'password'  => Hash::make('Admin@1234'),
                'role'      => 'admin',
                'is_admin'  => true,
                'is_active' => true,
                'phone'     => '+256700000001',
                'bio'       => 'Managing Partner and head of ONYX Legal. Oversees all firm operations and strategic direction.',
            ]
        );

        $officer1 = User::updateOrCreate(
            ['email' => 'sarah@onyxlegal.ug'],
            [
                'name'      => 'Sarah Nakato',
                'username'  => 'sarah.nakato',
                'password'  => Hash::make('Officer@1234'),
                'role'      => 'officer',
                'is_admin'  => false,
                'is_active' => true,
                'phone'     => '+256704112233',
                'bio'       => 'Senior Associate specialising in Land & Property and Commercial litigation. Called to the bar in 2018.',
            ]
        );

        $officer2 = User::updateOrCreate(
            ['email' => 'peter@onyxlegal.ug'],
            [
                'name'      => 'Peter Ochieng',
                'username'  => 'peter.ochieng',
                'password'  => Hash::make('Officer@1234'),
                'role'      => 'officer',
                'is_admin'  => false,
                'is_active' => true,
                'phone'     => '+256782334455',
                'bio'       => 'Associate specialising in Criminal Defence and Human Rights. Called to the bar in 2021.',
            ]
        );

        $officer3 = User::updateOrCreate(
            ['email' => 'grace@onyxlegal.ug'],
            [
                'name'      => 'Grace Apio',
                'username'  => 'grace.apio',
                'password'  => Hash::make('Officer@1234'),
                'role'      => 'officer',
                'is_admin'  => false,
                'is_active' => true,
                'phone'     => '+256755667788',
                'bio'       => 'Associate specialising in Family Law and Succession & Probate.',
            ]
        );

        $frontdesk = User::updateOrCreate(
            ['email' => 'james@onyxlegal.ug'],
            [
                'name'      => 'James Okello',
                'username'  => 'james.okello',
                'password'  => Hash::make('Desk@1234'),
                'role'      => 'frontdesk',
                'is_admin'  => false,
                'is_active' => true,
                'phone'     => '+256700000003',
                'bio'       => 'Front Desk and Client Relations Officer.',
            ]
        );

        // ── FINANCIAL PERIODS ──────────────────────────────────────────────────

        $period1 = FinancialPeriod::updateOrCreate(
            ['name' => 'FY 2024/2025'],
            [
                'start_date'  => '2024-07-01',
                'end_date'    => '2025-06-30',
                'is_active'   => false,
                'description' => 'Financial Year 2024 to 2025 — Closed',
                'created_by'  => $admin->id,
            ]
        );

        $period2 = FinancialPeriod::updateOrCreate(
            ['name' => 'FY 2025/2026'],
            [
                'start_date'  => '2025-07-01',
                'end_date'    => '2026-06-30',
                'is_active'   => true,
                'description' => 'Current financial year — July 2025 to June 2026',
                'created_by'  => $admin->id,
            ]
        );

        // ── ACCOUNTS ───────────────────────────────────────────────────────────

        $bankAccount = Account::updateOrCreate(
            ['name' => 'Stanbic Bank — Operations'],
            [
                'type'            => 'bank',
                'bank_name'       => 'Stanbic Bank Uganda',
                'account_number'  => '9030012345678',
                'branch'          => 'Main Branch, Kampala',
                'opening_balance' => 12500000,
                'description'     => 'Primary operating account for all firm transactions',
                'is_active'       => true,
                'created_by'      => $admin->id,
            ]
        );

        $cashAccount = Account::updateOrCreate(
            ['name' => 'Petty Cash — Front Office'],
            [
                'type'            => 'cash',
                'opening_balance' => 800000,
                'description'     => 'Day-to-day petty cash for office expenses',
                'is_active'       => true,
                'created_by'      => $admin->id,
            ]
        );

        $mobileAccount = Account::updateOrCreate(
            ['name' => 'MTN Mobile Money'],
            [
                'type'            => 'mobile_money',
                'account_number'  => '0700000001',
                'opening_balance' => 500000,
                'description'     => 'MTN MoMo account for client mobile payments',
                'is_active'       => true,
                'created_by'      => $admin->id,
            ]
        );

        // ── CLIENTS ────────────────────────────────────────────────────────────

        $clients = [];

        $clients[] = Client::updateOrCreate(['client_number' => 'CL-0001'], [
            'first_name'  => 'David',         'last_name'   => 'Ssemakula',
            'email'       => 'david.ssemakula@gmail.com',
            'phone'       => '+256772100001', 'phone_alt'   => '+256704100001',
            'gender'      => 'male',          'dob'         => '1975-03-12',
            'id_type'     => 'national_id',   'id_number'   => 'CM900123456YNDE',
            'address'     => 'Plot 12, Ntinda Road, Ntinda',
            'district'    => 'Kampala',       'occupation'  => 'Business Owner',
            'company'     => 'Ssemakula Enterprises Ltd',
            'notes'       => 'Long-standing client. Prefers to be contacted in the mornings.',
            'created_by'  => $frontdesk->id,
        ]);

        $clients[] = Client::updateOrCreate(['client_number' => 'CL-0002'], [
            'first_name'  => 'Fatuma',        'last_name'   => 'Namutebi',
            'email'       => 'fatuma.n@yahoo.com',
            'phone'       => '+256704200002',
            'gender'      => 'female',        'dob'         => '1983-07-22',
            'id_type'     => 'passport',      'id_number'   => 'A12345678',
            'address'     => 'Najjanankumbi, Kampala',
            'district'    => 'Kampala',       'occupation'  => 'Secondary School Teacher',
            'notes'       => 'Divorce proceedings — highly sensitive. Handle with discretion.',
            'created_by'  => $frontdesk->id,
        ]);

        $clients[] = Client::updateOrCreate(['client_number' => 'CL-0003'], [
            'first_name'  => 'Achilles',      'last_name'   => 'Otim',
            'phone'       => '+256782300003',
            'gender'      => 'male',          'dob'         => '1990-11-05',
            'id_type'     => 'national_id',   'id_number'   => 'CM901098765WNDE',
            'address'     => 'Gulu Municipal Council, Block 4',
            'district'    => 'Gulu',          'occupation'  => 'Farmer',
            'created_by'  => $frontdesk->id,
        ]);

        $clients[] = Client::updateOrCreate(['client_number' => 'CL-0004'], [
            'first_name'  => 'Rebecca',       'last_name'   => 'Atim Opiyo',
            'email'       => 'r.atim@email.com',
            'phone'       => '+256756400004',
            'gender'      => 'female',        'dob'         => '1968-01-30',
            'id_type'     => 'national_id',   'id_number'   => 'CF800054321YNDE',
            'address'     => 'Plot 45, Mawanda Road, Kamwokya',
            'district'    => 'Kampala',       'occupation'  => 'Retired Civil Servant',
            'notes'       => 'Estate dispute with siblings. Three siblings contesting the will.',
            'created_by'  => $frontdesk->id,
        ]);

        $clients[] = Client::updateOrCreate(['client_number' => 'CL-0005'], [
            'first_name'  => 'Hamid',         'last_name'   => 'Kateregga',
            'email'       => 'h.kateregga@kateregga-tech.co.ug',
            'phone'       => '+256774500005',
            'gender'      => 'male',          'dob'         => '1980-05-14',
            'id_type'     => 'national_id',   'id_number'   => 'CM800254321YNDE',
            'address'     => 'Workers House, Pilkington Road',
            'district'    => 'Kampala',       'occupation'  => 'IT Entrepreneur',
            'company'     => 'Kateregga Tech Solutions Ltd',
            'notes'       => 'Disputes with former business partner over IP and company shares.',
            'created_by'  => $admin->id,
        ]);

        $clients[] = Client::updateOrCreate(['client_number' => 'CL-0006'], [
            'first_name'  => 'Miriam',        'last_name'   => 'Nansubuga',
            'email'       => 'miriam.n@email.com',
            'phone'       => '+256700600006',
            'gender'      => 'female',        'dob'         => '1992-09-18',
            'id_type'     => 'driving_permit', 'id_number'  => 'UG/DL/2015/001234',
            'address'     => 'Bukoto, Kampala',
            'district'    => 'Kampala',       'occupation'  => 'Nurse',
            'notes'       => 'Wrongful dismissal case against Mulago Hospital.',
            'created_by'  => $frontdesk->id,
        ]);

        $clients[] = Client::updateOrCreate(['client_number' => 'CL-0007'], [
            'first_name'  => 'Emmanuel',      'last_name'   => 'Tumwesigye',
            'email'       => 'tumwesigye.e@gmail.com',
            'phone'       => '+256752700007',
            'gender'      => 'male',          'dob'         => '1978-12-03',
            'id_type'     => 'national_id',   'id_number'   => 'CM780312567WNDE',
            'address'     => 'Fort Portal Municipality, Rwenzori Street',
            'district'    => 'Fort Portal',   'occupation'  => 'Hotelier',
            'company'     => 'Rwenzori Highlands Hotel',
            'notes'       => 'Land dispute — 50 acres in Kasese District. High priority case.',
            'created_by'  => $frontdesk->id,
        ]);

        $clients[] = Client::updateOrCreate(['client_number' => 'CL-0008'], [
            'first_name'  => 'Agnes',         'last_name'   => 'Akello',
            'email'       => 'agnes.akello@email.com',
            'phone'       => '+256785800008',
            'gender'      => 'female',        'dob'         => '1955-06-20',
            'id_type'     => 'national_id',   'id_number'   => 'CF550620123YNDE',
            'address'     => 'Lira City, Erute Division',
            'district'    => 'Lira',          'occupation'  => 'Retired Teacher',
            'notes'       => 'Succession — husband died intestate. Three adult children involved.',
            'created_by'  => $frontdesk->id,
        ]);

        $clients[] = Client::updateOrCreate(['client_number' => 'CL-0009'], [
            'first_name'  => 'Robert',        'last_name'   => 'Mwesigwa',
            'email'       => 'r.mwesigwa@jinja-bricks.com',
            'phone'       => '+256702900009',
            'gender'      => 'male',          'dob'         => '1971-04-28',
            'id_type'     => 'national_id',   'id_number'   => 'CM710428999WNDE',
            'address'     => 'Jinja City, Iganga Road',
            'district'    => 'Jinja',         'occupation'  => 'Building Contractor',
            'company'     => 'Mwesigwa Building Works Ltd',
            'notes'       => 'Debt recovery from Kampala contractor who owes UGX 45 million.',
            'created_by'  => $frontdesk->id,
        ]);

        $clients[] = Client::updateOrCreate(['client_number' => 'CL-0010'], [
            'first_name'  => 'Patience',      'last_name'   => 'Amoding',
            'email'       => 'pamoding@ngo-heal.org',
            'phone'       => '+256778100010',
            'gender'      => 'female',        'dob'         => '1987-02-11',
            'id_type'     => 'passport',      'id_number'   => 'B09876543',
            'address'     => 'Moroto Municipality',
            'district'    => 'Moroto',        'occupation'  => 'NGO Programme Officer',
            'company'     => 'HEAL Uganda',
            'notes'       => 'Constitutional petition regarding land rights of Karamojong pastoralists.',
            'created_by'  => $admin->id,
        ]);

        $clients[] = Client::updateOrCreate(['client_number' => 'CL-0011'], [
            'first_name'  => 'Joseph',        'last_name'   => 'Byaruhanga',
            'email'       => 'joseph.bya@email.com',
            'phone'       => '+256704111111',
            'gender'      => 'male',          'dob'         => '1965-08-07',
            'id_type'     => 'national_id',   'id_number'   => 'CM650807444WNDE',
            'address'     => 'Mbarara City, Kakoba Division',
            'district'    => 'Mbarara',       'occupation'  => 'Retired Army Officer',
            'notes'       => 'Pension arrears dispute with Ministry of Defence.',
            'created_by'  => $frontdesk->id,
        ]);

        $clients[] = Client::updateOrCreate(['client_number' => 'CL-0012'], [
            'first_name'  => 'Zahara',        'last_name'   => 'Nabirye',
            'email'       => 'z.nabirye@email.com',
            'phone'       => '+256752222222',
            'gender'      => 'female',        'dob'         => '1995-03-25',
            'id_type'     => 'national_id',   'id_number'   => 'CF950325888YNDE',
            'address'     => 'Mukono Municipality, Gganda',
            'district'    => 'Mukono',        'occupation'  => 'Shop Owner',
            'notes'       => 'Accused of uttering false document at her workplace. Criminal defence.',
            'created_by'  => $frontdesk->id,
        ]);

        // ── CASES ──────────────────────────────────────────────────────────────

        $cases = [];

        // Case 1 — Active, In Court, Land
        $case1 = LegalCase::updateOrCreate(['case_number' => 'LC-0001'], [
            'title'             => 'Ssemakula v. Kawooya — Boundary Encroachment',
            'description'       => 'Client\'s neighbour has encroached approximately 0.25 acres of Plot 12, Ntinda. Boundary beacons were removed without consent. Client holds a valid Mailo land title.',
            'category'          => 'land_property',
            'status'            => 'active',
            'stage'             => 'trial',
            'priority'          => 'high',
            'client_id'         => $clients[0]->id,
            'main_officer_id'   => $officer1->id,
            'filing_date'       => '2025-09-10',
            'is_in_court'       => true,
            'court_name'        => 'High Court of Uganda — Land Division',
            'court_division'    => 'Land Division',
            'court_case_number' => 'HCLD/CV/0123/2025',
            'judge_name'        => 'Hon. Justice Margaret Ogwal',
            'next_hearing_date' => '2026-07-14',
            'is_at_police'      => false,
            'created_by'        => $frontdesk->id,
        ]);
        $cases[] = $case1;

        // Case 2 — Ongoing, Mediation, Family
        $case2 = LegalCase::updateOrCreate(['case_number' => 'LC-0002'], [
            'title'           => 'Namutebi Matrimonial Property Division',
            'description'     => 'Client seeks equitable division of matrimonial property including a home in Najjanankumbi and a residential plot in Gayaza following divorce granted by the Family Division on 15 March 2025.',
            'category'        => 'family_law',
            'status'          => 'ongoing',
            'stage'           => 'mediation',
            'priority'        => 'medium',
            'client_id'       => $clients[1]->id,
            'main_officer_id' => $officer3->id,
            'filing_date'     => '2025-11-20',
            'is_in_court'     => true,
            'court_name'      => 'Family and Children Court',
            'court_division'  => 'Family Division',
            'court_case_number' => 'FCD/DIV/0087/2025',
            'judge_name'      => 'Hon. Lady Justice Anita Amulen',
            'next_hearing_date' => '2026-06-28',
            'is_at_police'    => false,
            'created_by'      => $frontdesk->id,
        ]);
        $cases[] = $case2;

        // Case 3 — Pending, Criminal, at Police
        $case3 = LegalCase::updateOrCreate(['case_number' => 'LC-0003'], [
            'title'                => 'Otim — Criminal Defence (Assault)',
            'description'          => 'Client was arrested following an altercation at a local market in Gulu on 22 May 2026. Police have filed an OB report. Charge is causing grievous bodily harm contrary to S.224 Penal Code Act.',
            'category'             => 'criminal_defense',
            'status'               => 'active',
            'stage'                => 'investigation',
            'priority'             => 'urgent',
            'client_id'            => $clients[2]->id,
            'main_officer_id'      => $officer2->id,
            'filing_date'          => '2026-05-23',
            'is_in_court'          => false,
            'is_at_police'         => true,
            'police_station'       => 'Gulu Central Police Station',
            'police_ref_number'    => 'OB/2026/05/234',
            'investigating_officer' => 'D/ASP Patrick Okello',
            'created_by'           => $frontdesk->id,
        ]);
        $cases[] = $case3;

        // Case 4 — Closed/Won, Succession
        $case4 = LegalCase::updateOrCreate(['case_number' => 'LC-0004'], [
            'title'           => 'Rebecca Atim — Estate of Late Opiyo Charles',
            'description'     => 'Administration of the estate of the late Opiyo Charles who died intestate in August 2024. Client is the surviving widow. Three adult children are contesting.',
            'category'        => 'succession_probate',
            'status'          => 'closed',
            'stage'           => 'closed',
            'priority'        => 'medium',
            'client_id'       => $clients[3]->id,
            'main_officer_id' => $officer3->id,
            'filing_date'     => '2024-09-05',
            'closed_date'     => '2026-03-20',
            'score'           => 1,
            'closing_remarks' => 'Letters of Administration granted in favour of client. Widow appointed sole administrator. All three children withdrew objections following mediation session on 12 March 2026.',
            'is_in_court'     => false,
            'is_at_police'    => false,
            'created_by'      => $admin->id,
        ]);
        $cases[] = $case4;

        // Case 5 — Ongoing, Commercial, In Court
        $case5 = LegalCase::updateOrCreate(['case_number' => 'LC-0005'], [
            'title'             => 'Kateregga Tech v. Kizza Samuel — IP & Shareholding Dispute',
            'description'       => 'Former business partner Kizza Samuel is wrongfully claiming 40% shareholding in client\'s tech company and threatening to copy proprietary software source code. Seeking injunction and declaratory orders.',
            'category'          => 'commercial_corporate',
            'status'            => 'active',
            'stage'             => 'pre_trial',
            'priority'          => 'urgent',
            'client_id'         => $clients[4]->id,
            'main_officer_id'   => $officer1->id,
            'filing_date'       => '2026-01-08',
            'is_in_court'       => true,
            'court_name'        => 'High Court of Uganda — Commercial Division',
            'court_division'    => 'Commercial Division',
            'court_case_number' => 'HCCS/0034/2026',
            'judge_name'        => 'Hon. Justice Stephen Mubiru',
            'next_hearing_date' => '2026-06-17',
            'is_at_police'      => false,
            'created_by'        => $admin->id,
        ]);
        $cases[] = $case5;

        // Case 6 — Active, Employment
        $case6 = LegalCase::updateOrCreate(['case_number' => 'LC-0006'], [
            'title'           => 'Nansubuga Miriam v. Mulago Hospital — Wrongful Dismissal',
            'description'     => 'Client was dismissed without due process after raising concerns about medical waste disposal practices. No prior warning or disciplinary hearing was conducted. Seeking reinstatement and 24 months\' salary in lieu.',
            'category'        => 'employment_labour',
            'status'          => 'ongoing',
            'stage'           => 'trial',
            'priority'        => 'high',
            'client_id'       => $clients[5]->id,
            'main_officer_id' => $officer2->id,
            'filing_date'     => '2025-08-14',
            'is_in_court'     => true,
            'court_name'      => 'Industrial Court of Uganda',
            'court_division'  => 'Employment & Labour Relations',
            'court_case_number' => 'IC/LRD/0456/2025',
            'judge_name'      => 'Hon. Judge Robert Bamwine',
            'next_hearing_date' => '2026-07-01',
            'is_at_police'    => false,
            'created_by'      => $frontdesk->id,
        ]);
        $cases[] = $case6;

        // Case 7 — Active, Land, Priority High
        $case7 = LegalCase::updateOrCreate(['case_number' => 'LC-0007'], [
            'title'           => 'Tumwesigye Emmanuel — Kasese Land Fraud',
            'description'     => 'Client\'s 50-acre plot in Bwera, Kasese was fraudulently transferred to a third party using forged documents. Client holds original title. Seeking cancellation of fraudulent title and recovery of land.',
            'category'        => 'land_property',
            'status'          => 'active',
            'stage'           => 'investigation',
            'priority'        => 'high',
            'client_id'       => $clients[6]->id,
            'main_officer_id' => $officer1->id,
            'filing_date'     => '2026-02-17',
            'is_in_court'     => false,
            'is_at_police'    => true,
            'police_station'  => 'Kasese CID Headquarters',
            'police_ref_number' => 'CID/KASESE/2026/021',
            'investigating_officer' => 'D/IP Samuel Kato',
            'created_by'      => $frontdesk->id,
        ]);
        $cases[] = $case7;

        // Case 8 — Pending, Succession
        $case8 = LegalCase::updateOrCreate(['case_number' => 'LC-0008'], [
            'title'           => 'Akello Agnes — Succession (Lira District)',
            'description'     => 'Client\'s late husband died without a will. The estate includes a three-bedroom home in Lira and agricultural land in Erute. Three adult children from a previous marriage are contesting.',
            'category'        => 'succession_probate',
            'status'          => 'pending',
            'stage'           => 'intake',
            'priority'        => 'medium',
            'client_id'       => $clients[7]->id,
            'main_officer_id' => $officer3->id,
            'filing_date'     => '2026-04-30',
            'is_in_court'     => false,
            'is_at_police'    => false,
            'created_by'      => $frontdesk->id,
        ]);
        $cases[] = $case8;

        // Case 9 — Active, Debt Recovery
        $case9 = LegalCase::updateOrCreate(['case_number' => 'LC-0009'], [
            'title'             => 'Mwesigwa v. Ssali Construction — Debt Recovery UGX 45M',
            'description'       => 'Client supplied building materials worth UGX 45,000,000 to Ssali Construction in Kampala. Multiple demands have been ignored. Seeking summary judgment and attachment of assets.',
            'category'          => 'debt_recovery',
            'status'            => 'ongoing',
            'stage'             => 'pre_trial',
            'priority'          => 'high',
            'client_id'         => $clients[8]->id,
            'main_officer_id'   => $officer1->id,
            'filing_date'       => '2026-01-25',
            'is_in_court'       => true,
            'court_name'        => 'High Court — Civil Division (Jinja)',
            'court_division'    => 'Civil Division',
            'court_case_number' => 'HCCS/JIN/0011/2026',
            'judge_name'        => 'Hon. Justice Grace Nambatya',
            'next_hearing_date' => '2026-06-10',
            'is_at_police'      => false,
            'created_by'        => $frontdesk->id,
        ]);
        $cases[] = $case9;

        // Case 10 — Active, Constitutional
        $case10 = LegalCase::updateOrCreate(['case_number' => 'LC-0010'], [
            'title'             => 'Amoding & Others v. AG — Karamoja Pastoralist Land Rights',
            'description'       => 'Constitutional petition challenging the government\'s eviction of Karamojong pastoralists from communal grazing land in Moroto without compensation or consultation, contrary to Articles 26, 29 and 237 of the Constitution.',
            'category'          => 'constitutional',
            'status'            => 'active',
            'stage'             => 'trial',
            'priority'          => 'high',
            'client_id'         => $clients[9]->id,
            'main_officer_id'   => $admin->id,
            'filing_date'       => '2025-10-01',
            'is_in_court'       => true,
            'court_name'        => 'Constitutional Court of Uganda',
            'court_division'    => 'Constitutional Court',
            'court_case_number' => 'CONST/PET/0012/2025',
            'judge_name'        => 'Hon. Justice Kenneth Kakuru (Panel of 5)',
            'next_hearing_date' => '2026-07-08',
            'is_at_police'      => false,
            'created_by'        => $admin->id,
        ]);
        $cases[] = $case10;

        // Case 11 — Active, Employment (Pension)
        $case11 = LegalCase::updateOrCreate(['case_number' => 'LC-0011'], [
            'title'           => 'Byaruhanga Joseph v. Ministry of Defence — Pension Arrears',
            'description'     => 'Client retired from the UPDF in 2020 but has received no pension payments to date. The Ministry disputes the computation period. Seeking UGX 36,000,000 in arrears plus monthly pension of UGX 1,200,000.',
            'category'        => 'employment_labour',
            'status'          => 'active',
            'stage'           => 'mediation',
            'priority'        => 'medium',
            'client_id'       => $clients[10]->id,
            'main_officer_id' => $officer2->id,
            'filing_date'     => '2026-03-12',
            'is_in_court'     => false,
            'is_at_police'    => false,
            'created_by'      => $frontdesk->id,
        ]);
        $cases[] = $case11;

        // Case 12 — Active, Criminal
        $case12 = LegalCase::updateOrCreate(['case_number' => 'LC-0012'], [
            'title'             => 'Nabirye Zahara — Uttering False Document Defence',
            'description'       => 'Client is accused of presenting a forged degree certificate during a job application at a Mukono-based school. Client maintains innocence and alleges the document was a certified copy she received from a third party.',
            'category'          => 'criminal_defense',
            'status'            => 'active',
            'stage'             => 'trial',
            'priority'          => 'high',
            'client_id'         => $clients[11]->id,
            'main_officer_id'   => $officer2->id,
            'filing_date'       => '2026-02-03',
            'is_in_court'       => true,
            'court_name'        => 'Chief Magistrate\'s Court — Mukono',
            'court_division'    => 'Criminal Division',
            'court_case_number' => 'CM/CR/0089/2026',
            'judge_name'        => 'Hon. Chief Magistrate Susan Namutebi',
            'next_hearing_date' => '2026-06-22',
            'is_at_police'      => false,
            'created_by'        => $frontdesk->id,
        ]);
        $cases[] = $case12;

        // Case 13 — Closed/Lost, Civil Litigation
        $case13 = LegalCase::updateOrCreate(['case_number' => 'LC-0013'], [
            'title'           => 'Ssemakula v. Insurance Co. — Motor Accident Claim',
            'description'     => 'Client sought compensation of UGX 22,000,000 for injuries sustained in a motor vehicle accident on Jinja Road in January 2024. Insurance company disputed fault.',
            'category'        => 'civil_litigation',
            'status'          => 'closed',
            'stage'           => 'closed',
            'priority'        => 'low',
            'client_id'       => $clients[0]->id,
            'main_officer_id' => $officer1->id,
            'filing_date'     => '2024-03-01',
            'closed_date'     => '2025-11-15',
            'score'           => -1,
            'closing_remarks' => 'Court ruled in favour of the insurance company, finding contributory negligence on the part of our client (25 km/h above speed limit). Appeal is under consideration.',
            'is_in_court'     => false,
            'is_at_police'    => false,
            'created_by'      => $officer1->id,
        ]);
        $cases[] = $case13;

        // Case 14 — Closed/Neutral, Family
        $case14 = LegalCase::updateOrCreate(['case_number' => 'LC-0014'], [
            'title'           => 'Namutebi — Child Custody Agreement',
            'description'     => 'Custody arrangement for three minor children following divorce. Both parents agreed to joint custody with primary residence at the mother\'s home and weekend visitation for the father.',
            'category'        => 'family_law',
            'status'          => 'closed',
            'stage'           => 'closed',
            'priority'        => 'medium',
            'client_id'       => $clients[1]->id,
            'main_officer_id' => $officer3->id,
            'filing_date'     => '2025-04-12',
            'closed_date'     => '2025-12-10',
            'score'           => 0,
            'closing_remarks' => 'Consent order filed and approved by the court. Settlement achieved through mediation without full trial. Client satisfied with the arrangement.',
            'is_in_court'     => false,
            'is_at_police'    => false,
            'created_by'      => $frontdesk->id,
        ]);
        $cases[] = $case14;

        // Case 15 — Active, Human Rights
        $case15 = LegalCase::updateOrCreate(['case_number' => 'LC-0015'], [
            'title'           => 'Amoding — Unlawful Detention at Moroto Police',
            'description'     => 'Client and two colleagues were unlawfully detained for 72 hours without charge following a land rights protest. Seeking compensation from the AG for breach of Article 23 of the Constitution.',
            'category'        => 'human_rights',
            'status'          => 'active',
            'stage'           => 'pre_trial',
            'priority'        => 'high',
            'client_id'       => $clients[9]->id,
            'main_officer_id' => $admin->id,
            'filing_date'     => '2026-04-05',
            'is_in_court'     => true,
            'court_name'      => 'High Court of Uganda — Civil Division (Moroto)',
            'court_division'  => 'Civil Division',
            'court_case_number' => 'HCMA/0014/2026',
            'judge_name'      => 'Hon. Justice Christine Nakawunde',
            'next_hearing_date' => '2026-06-30',
            'is_at_police'    => false,
            'created_by'      => $admin->id,
        ]);
        $cases[] = $case15;

        // ── CASE OFFICERS (assign team members) ────────────────────────────────

        // Case 1: Sarah (main) + Peter (team)
        $case1->officers()->syncWithoutDetaching([
            $officer1->id => ['role' => 'main'],
            $officer2->id => ['role' => 'team'],
        ]);
        // Case 5: Sarah (main) + Admin (team)
        $case5->officers()->syncWithoutDetaching([
            $officer1->id => ['role' => 'main'],
            $admin->id    => ['role' => 'team'],
        ]);
        // Case 10: Admin (main) + Grace (team) + Peter (team)
        $case10->officers()->syncWithoutDetaching([
            $admin->id    => ['role' => 'main'],
            $officer3->id => ['role' => 'team'],
            $officer2->id => ['role' => 'team'],
        ]);
        // Case 12: Peter (main) + Grace (team)
        $case12->officers()->syncWithoutDetaching([
            $officer2->id => ['role' => 'main'],
            $officer3->id => ['role' => 'team'],
        ]);

        // ── CASE NOTES ─────────────────────────────────────────────────────────

        $this->addNotes($case1, [
            [$officer1->id, 'Initial site visit conducted at Plot 12, Ntinda. Boundary beacons confirmed removed. Photographs taken and surveyor engaged.', '2025-09-12'],
            [$officer1->id, 'Survey report received from Eng. Paul Kato. Report confirms encroachment of 0.27 acres. Report filed with court.', '2025-10-05'],
            [$officer2->id, 'Attended court — Respondent\'s counsel requested 30-day adjournment to file defence. Granted. Next date 14 November 2025.', '2025-10-20'],
            [$admin->id,    'Defence filed. Respondent denies encroachment and claims to hold a customary certificate. We shall challenge authenticity.', '2025-11-18', true],
            [$officer1->id, 'Witness statements collected from three neighbours. All support our client\'s position on the boundary.', '2026-01-30'],
            [$officer1->id, 'Expert witness — surveyor scheduled for testimony on 14 July 2026. Client briefed on what to expect at trial.', '2026-05-20'],
        ]);

        $this->addNotes($case2, [
            [$officer3->id, 'Initial consultation completed. Client seeks 50/50 division of matrimonial home and plot. Husband has offered 30/70 in his favour. Client rejected.', '2025-11-22'],
            [$officer3->id, 'Mediation session 1 — both parties present. Mediator Hon. Justice (Rtd) Bamugemereire. No agreement reached on property valuation.', '2026-01-15'],
            [$officer3->id, 'Valuation report received. Home valued at UGX 320,000,000, plot at UGX 85,000,000. Both valuations filed.', '2026-02-28'],
            [$admin->id,    'Client called — she will accept 45% of total estate value (UGX 182,250,000) to expedite closure. Do NOT share with opposing counsel yet.', '2026-03-10', true],
            [$officer3->id, 'Mediation session 3 — agreement close. Husband offered 43%. Client declined. Next session 28 June 2026.', '2026-04-20'],
        ]);

        $this->addNotes($case3, [
            [$officer2->id, 'Visited client at Gulu Central Police Station. Client states the altercation was in self-defence. Victim sustained minor injuries only.', '2026-05-24'],
            [$officer2->id, 'Obtained OB report from Police. Charge reads S.224 PCA — GBH. Medical examination report requested.', '2026-05-25'],
            [$admin->id,    'Medical report shows victim sustained a bruised forearm only. No fractures. We can push for S.219 (Common Assault) which carries lower penalty.', '2026-05-28', true],
        ]);

        $this->addNotes($case4, [
            [$officer3->id, 'Letters of Administration application filed at High Court Jinja Circuit.', '2024-09-08'],
            [$officer3->id, 'Mediation session with three children arranged for 12 March 2026 at ODAC.', '2026-02-10'],
            [$officer3->id, 'All three children agreed to withdraw objections. Consent order to be filed.', '2026-03-13'],
            [$officer3->id, 'Letters of Administration granted. Case closed. Client has been advised to draft a will promptly.', '2026-03-20'],
        ]);

        $this->addNotes($case5, [
            [$admin->id,    'Ex-parte injunction application heard. Court granted interim injunction restraining Kizza from accessing or copying source code. Police letter issued.', '2026-01-12'],
            [$officer1->id, 'Served injunction order on Kizza Samuel. Acknowledged receipt.', '2026-01-15'],
            [$admin->id,    'Kizza\'s counsel filed Notice of Appointment. They will oppose injunction. Hearing set for 17 February 2026.', '2026-01-20'],
            [$officer1->id, 'Injunction upheld. Kizza directed to file defence within 21 days. Client very pleased with outcome.', '2026-02-17'],
            [$officer1->id, 'Defence filed. Kizza claims verbal agreement for 40% shares. We are confident this cannot be proven without written documentation.', '2026-03-10'],
        ]);

        $this->addNotes($case6, [
            [$officer2->id, 'Client provided all employment records, warning letters (none exist), and dismissal letter. Clearly procedurally defective.', '2025-08-16'],
            [$officer2->id, 'Labour Officer conciliation failed. Certificate of Failure issued. Case referred to Industrial Court.', '2025-10-30'],
            [$officer2->id, 'Witness — client\'s supervisor agreed to testify that no disciplinary hearing was ever held. Statement recorded.', '2026-01-22'],
            [$admin->id,    'Mulago Legal team offered UGX 12,000,000 settlement. Client rejected. We are aiming for UGX 30,000,000+.', '2026-04-05', true],
        ]);

        $this->addNotes($case9, [
            [$officer1->id, 'Demand letter sent by registered post and email to Ssali Construction Ltd. Deadline: 14 February 2026.', '2026-01-28'],
            [$officer1->id, 'No response received by deadline. Filed plaint and Summary Judgment application at High Court Jinja.', '2026-02-16'],
            [$officer1->id, 'Respondent filed defence denying receipt of materials. We have 12 delivery notes and two witnesses. Summary Judgment likely to succeed.', '2026-04-12'],
        ]);

        $this->addNotes($case10, [
            [$admin->id,    'Petition filed at Constitutional Court. Coalition of 14 NGOs filing amicus curiae briefs in support.', '2025-10-03'],
            [$officer3->id, 'AG filed preliminary objection — we filed written submissions opposing. Court scheduled to rule on objection first.', '2026-01-20'],
            [$admin->id,    'Preliminary objection dismissed. Full hearing proceeds. Government has 60 days to file response.', '2026-03-08'],
            [$admin->id,    'Media coverage in Daily Monitor and New Vision has increased public awareness. Maintaining strict client confidentiality.', '2026-04-30', true],
        ]);

        // ── DOCUMENTS (metadata only — files are placeholders) ─────────────────

        $docData = [
            // Case 1
            ['LC-0001 — Mailo Land Title (Original Scan)', 'land_title',        $case1->id,  $clients[0]->id, $officer1->id],
            ['Survey Report — Eng. Paul Kato (Oct 2025)',  'evidence',          $case1->id,  $clients[0]->id, $officer1->id],
            ['LC-0001 — Plaint and Annexures',             'pleadings',         $case1->id,  null,            $officer1->id],
            ['Witness Statements x3 (LC-0001)',            'affidavit',         $case1->id,  null,            $officer1->id],
            // Case 2
            ['Divorce Decree — FCD/DIV/0087/2025',        'judgment',          $case2->id,  $clients[1]->id, $officer3->id],
            ['Valuation Report — Najjanankumbi Home',      'evidence',          $case2->id,  null,            $officer3->id],
            // Case 3
            ['OB Report — Gulu Central PS (OB/2026/05/234)', 'police_report',   $case3->id,  $clients[2]->id, $officer2->id],
            ['Medical Examination Report — Otim Achilles', 'evidence',          $case3->id,  null,            $officer2->id],
            // Case 4
            ['Death Certificate — Late Opiyo Charles',    'id_documents',      $case4->id,  $clients[3]->id, $officer3->id],
            ['Letters of Administration — Granted',       'court_order',       $case4->id,  $clients[3]->id, $officer3->id],
            // Case 5
            ['Injunction Order — HCCS/0034/2026',         'court_order',       $case5->id,  null,            $admin->id],
            ['Software Licence Agreement (Original)',      'contract_agreement',$case5->id,  $clients[4]->id, $officer1->id],
            // Case 6
            ['Dismissal Letter — Mulago (Aug 2025)',       'correspondence',    $case6->id,  $clients[5]->id, $officer2->id],
            ['Employment Contract — Nansubuga Miriam',     'contract_agreement',$case6->id,  null,            $officer2->id],
            // Case 10
            ['Constitutional Petition — CONST/PET/0012',  'pleadings',         $case10->id, null,            $admin->id],
            ['AG Response — Filed March 2026',             'correspondence',    $case10->id, null,            $admin->id],
            // Standalone documents (no case)
            ['ONYX Legal Standard Retainer Agreement',    'contract_agreement', null, null,            $admin->id],
            ['Power of Attorney — Ssemakula David',        'power_of_attorney', null, $clients[0]->id, $officer1->id],
            ['Power of Attorney — Kateregga Hamid',        'power_of_attorney', null, $clients[4]->id, $admin->id],
        ];

        foreach ($docData as $i => [$title, $cat, $caseId, $clientId, $uploaderId]) {
            $num = 'DOC-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT);
            Document::firstOrCreate(['doc_number' => $num], [
                'title'           => $title,
                'category'        => $cat,
                'case_id'         => $caseId,
                'client_id'       => $clientId,
                'file_path'       => 'documents/placeholder-' . ($i + 1) . '.pdf',
                'file_name'       => $num . '-' . Str::slug($title) . '.pdf',
                'file_size'       => rand(45000, 4500000),
                'mime_type'       => 'application/pdf',
                'description'     => null,
                'is_confidential' => in_array($cat, ['legal_opinion', 'affidavit']),
                'uploaded_by'     => $uploaderId,
            ]);
        }

        // ── TRANSACTIONS ───────────────────────────────────────────────────────

        $txns = [
            // Income — current period
            ['TXN-00001', 'RCP-00001', 'income',  3000000, 'Legal fees deposit — LC-0001 (Land Division)', $bankAccount->id,   $case1->id,  $clients[0]->id, $period2->id, 'bank_transfer', 'REF/STD/2025/001', '2025-09-15', $frontdesk->id],
            ['TXN-00002', 'RCP-00002', 'income',  1500000, 'Consultation retainer — LC-0002 (Family)',     $cashAccount->id,   $case2->id,  $clients[1]->id, $period2->id, 'cash',          null,               '2025-11-22', $frontdesk->id],
            ['TXN-00003', 'RCP-00003', 'income',  5000000, 'Litigation fees — LC-0005 (Commercial)',       $bankAccount->id,   $case5->id,  $clients[4]->id, $period2->id, 'bank_transfer', 'REF/STD/2026/005', '2026-01-10', $frontdesk->id],
            ['TXN-00004', 'RCP-00004', 'income',  2500000, 'Retainer — LC-0006 (Employment)',              $mobileAccount->id, $case6->id,  $clients[5]->id, $period2->id, 'mobile_money',  'MTN/2026/1234',    '2026-02-05', $frontdesk->id],
            ['TXN-00005', 'RCP-00005', 'income',  4000000, 'Legal fees — LC-0007 (Land Fraud)',            $bankAccount->id,   $case7->id,  $clients[6]->id, $period2->id, 'bank_transfer', 'REF/STD/2026/007', '2026-02-20', $frontdesk->id],
            ['TXN-00006', 'RCP-00006', 'income',  1000000, 'Filing fee reimbursement — LC-0009',           $cashAccount->id,   $case9->id,  $clients[8]->id, $period2->id, 'cash',          null,               '2026-02-18', $frontdesk->id],
            ['TXN-00007', 'RCP-00007', 'income',  6000000, 'Retainer fees — LC-0010 (Constitutional)',     $bankAccount->id,   $case10->id, $clients[9]->id, $period2->id, 'cheque',        'CHQ/STD/2025/089', '2025-10-05', $admin->id],
            ['TXN-00008', 'RCP-00008', 'income',  1800000, 'Professional fees — LC-0003 (Criminal)',       $cashAccount->id,   $case3->id,  $clients[2]->id, $period2->id, 'cash',          null,               '2026-05-24', $frontdesk->id],
            ['TXN-00009', 'RCP-00009', 'income',  2200000, 'Additional fees — LC-0001 (Land)',             $bankAccount->id,   $case1->id,  $clients[0]->id, $period2->id, 'bank_transfer', 'REF/STD/2026/012', '2026-03-01', $frontdesk->id],
            ['TXN-00010', 'RCP-00010', 'income',  800000,  'Filing and court dues — LC-0012',              $cashAccount->id,   $case12->id, $clients[11]->id,$period2->id, 'cash',          null,               '2026-02-05', $frontdesk->id],
            ['TXN-00011', 'RCP-00011', 'income',  1200000, 'Consultation — LC-0011 (Pension)',             $mobileAccount->id, $case11->id, $clients[10]->id,$period2->id, 'mobile_money',  'MTN/2026/5678',    '2026-03-15', $frontdesk->id],
            ['TXN-00012', 'RCP-00012', 'income',  3500000, 'Retainer — LC-0015 (Human Rights)',            $bankAccount->id,   $case15->id, $clients[9]->id, $period2->id, 'bank_transfer', 'REF/STD/2026/015', '2026-04-10', $admin->id],
            ['TXN-00013', 'RCP-00013', 'income',  1500000, 'Succession petition fees — LC-0008',           $cashAccount->id,   $case8->id,  $clients[7]->id, $period2->id, 'cash',          null,               '2026-05-02', $frontdesk->id],

            // Expenses — current period
            ['TXN-00014', null, 'expense', 350000,  'Court filing fees — HCLD/CV/0123/2025',           $bankAccount->id,   $case1->id,  null, $period2->id, 'bank_transfer', null,               '2025-09-12', $officer1->id],
            ['TXN-00015', null, 'expense', 180000,  'Surveyor fee — Eng. Paul Kato',                   $cashAccount->id,   $case1->id,  null, $period2->id, 'cash',          null,               '2025-09-15', $officer1->id],
            ['TXN-00016', null, 'expense', 85000,   'Court filing — FCD/DIV/0087/2025',                $bankAccount->id,   $case2->id,  null, $period2->id, 'bank_transfer', null,               '2025-11-25', $officer3->id],
            ['TXN-00017', null, 'expense', 250000,  'Filing fees — HCCS/0034/2026 (Injunction)',       $bankAccount->id,   $case5->id,  null, $period2->id, 'bank_transfer', null,               '2026-01-09', $admin->id],
            ['TXN-00018', null, 'expense', 150000,  'Filing fees — IC/LRD/0456/2025',                  $bankAccount->id,   $case6->id,  null, $period2->id, 'bank_transfer', null,               '2025-11-03', $officer2->id],
            ['TXN-00019', null, 'expense', 450000,  'Constitutional Court filing — CONST/PET/0012',    $bankAccount->id,   $case10->id, null, $period2->id, 'bank_transfer', null,               '2025-10-01', $admin->id],
            ['TXN-00020', null, 'expense', 1200000, 'Office rent — May 2026',                          $bankAccount->id,   null,        null, $period2->id, 'bank_transfer', 'RENT/2026/05',    '2026-05-01', $admin->id],
            ['TXN-00021', null, 'expense', 380000,  'Electricity & utilities — April 2026',            $cashAccount->id,   null,        null, $period2->id, 'cash',          null,               '2026-04-05', $frontdesk->id],
            ['TXN-00022', null, 'expense', 620000,  'Office stationery & printing — Q1 2026',          $cashAccount->id,   null,        null, $period2->id, 'cash',          null,               '2026-04-15', $frontdesk->id],
            ['TXN-00023', null, 'expense', 95000,   'Gulu travel & accommodation — LC-0003',           $cashAccount->id,   $case3->id,  null, $period2->id, 'cash',          null,               '2026-05-24', $officer2->id],
            ['TXN-00024', null, 'expense', 200000,  'CID filing — Kasese (LC-0007)',                   $bankAccount->id,   $case7->id,  null, $period2->id, 'bank_transfer', null,               '2026-02-18', $officer1->id],
            ['TXN-00025', null, 'expense', 1200000, 'Office rent — April 2026',                        $bankAccount->id,   null,        null, $period2->id, 'bank_transfer', 'RENT/2026/04',    '2026-04-01', $admin->id],

            // Previous period (FY 2024/2025)
            ['TXN-00026', 'RCP-00014', 'income',  2800000, 'Legal fees — Estate LC-0004 (Succession)',  $bankAccount->id,   $case4->id,  $clients[3]->id, $period1->id, 'bank_transfer', 'REF/STD/2024/034', '2024-09-10', $officer3->id],
            ['TXN-00027', 'RCP-00015', 'income',  3200000, 'Fees — LC-0013 (Civil Litigation)',         $bankAccount->id,   $case13->id, $clients[0]->id, $period1->id, 'bank_transfer', 'REF/STD/2024/035', '2024-03-05', $officer1->id],
            ['TXN-00028', 'RCP-00016', 'income',  1400000, 'Custody fees — LC-0014',                   $cashAccount->id,   $case14->id, $clients[1]->id, $period1->id, 'cash',          null,               '2025-04-15', $frontdesk->id],
            ['TXN-00029', null,         'expense', 200000,  'High Court filing — LC-0013',              $bankAccount->id,   $case13->id, null,            $period1->id, 'bank_transfer', null,               '2024-03-02', $officer1->id],
            ['TXN-00030', null,         'expense', 1200000, 'Annual office rent payment — FY 2024/25', $bankAccount->id,   null,        null,            $period1->id, 'bank_transfer', 'RENT/2024/JUL',    '2024-07-01', $admin->id],
        ];

        foreach ($txns as [$num, $rcp, $type, $amount, $desc, $acctId, $caseId, $clientId, $periodId, $method, $ref, $date, $creator]) {
            Transaction::firstOrCreate(['transaction_number' => $num], [
                'receipt_number'      => $rcp,
                'type'                => $type,
                'amount'              => $amount,
                'description'         => $desc,
                'account_id'          => $acctId,
                'case_id'             => $caseId,
                'client_id'           => $clientId,
                'financial_period_id' => $periodId,
                'payment_method'      => $method,
                'reference_number'    => $ref,
                'transaction_date'    => $date,
                'created_by'          => $creator,
            ]);
        }

        // ── SUMMARY ────────────────────────────────────────────────────────────

        $this->command->newLine();
        $this->command->info('╔══════════════════════════════════════════════════╗');
        $this->command->info('║         ONYX Legal — Seed Data Complete          ║');
        $this->command->info('╠══════════════════════════════════════════════════╣');
        $this->command->info('║  Admin:     admin@onyxlegal.ug / Admin@1234      ║');
        $this->command->info('║  Officer:   sarah@onyxlegal.ug / Officer@1234    ║');
        $this->command->info('║  Officer:   peter@onyxlegal.ug / Officer@1234    ║');
        $this->command->info('║  Officer:   grace@onyxlegal.ug / Officer@1234    ║');
        $this->command->info('║  Frontdesk: james@onyxlegal.ug / Desk@1234       ║');
        $this->command->info('╠══════════════════════════════════════════════════╣');
        $this->command->info('║  Clients:      ' . Client::count() . '                              ║');
        $this->command->info('║  Cases:        ' . LegalCase::count() . '                             ║');
        $this->command->info('║  Documents:    ' . Document::count() . '                             ║');
        $this->command->info('║  Transactions: ' . Transaction::count() . '                             ║');
        $this->command->info('╚══════════════════════════════════════════════════╝');
    }

    private function addNotes(LegalCase $case, array $notes): void
    {
        foreach ($notes as $noteData) {
            [$userId, $text, $date] = $noteData;
            $isPrivate = $noteData[3] ?? false;

            CaseNote::firstOrCreate(
                ['case_id' => $case->id, 'user_id' => $userId, 'note' => $text],
                [
                    'is_private' => $isPrivate,
                    'created_at' => $date . ' 09:00:00',
                    'updated_at' => $date . ' 09:00:00',
                ]
            );
        }
    }
}
