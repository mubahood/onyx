<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use App\Models\Course;
use App\Models\Student;
use App\Models\Instructor;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\User;
use App\Models\Chat;
use App\Models\Message;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        // ─────────────────────────────────────────────
        // 1. COURSES
        // ─────────────────────────────────────────────
        $courses = [
            [
                'name'        => 'Web Development Fundamentals',
                'slug'        => 'web-development-fundamentals',
                'category'    => 'Web Development',
                'description' => 'Learn the building blocks of the web: HTML, CSS, and JavaScript from scratch.',
                'fee'         => 350000,
                'weekly_outline' => json_encode(['HTML5 & Semantics', 'CSS3 & Flexbox', 'JavaScript Basics', 'Responsive Design', 'Git & GitHub']),
            ],
            [
                'name'        => 'Python Programming',
                'slug'        => 'python-programming',
                'category'    => 'Programming',
                'description' => 'Master Python for scripting, automation, and back-end development.',
                'fee'         => 400000,
                'weekly_outline' => json_encode(['Python Syntax', 'OOP in Python', 'File Handling', 'APIs with Flask', 'Databases with SQLAlchemy']),
            ],
            [
                'name'        => 'Data Science & Analytics',
                'slug'        => 'data-science-analytics',
                'category'    => 'Data Science',
                'description' => 'Analyse data and build predictive models using Python, Pandas, and ML libraries.',
                'fee'         => 500000,
                'weekly_outline' => json_encode(['NumPy & Pandas', 'Data Visualisation', 'Statistics', 'Machine Learning', 'Capstone Project']),
            ],
            [
                'name'        => 'Mobile App Development',
                'slug'        => 'mobile-app-development',
                'category'    => 'Mobile',
                'description' => 'Build cross-platform mobile applications using Flutter and Dart.',
                'fee'         => 450000,
                'weekly_outline' => json_encode(['Dart Basics', 'Flutter Widgets', 'State Management', 'REST API Integration', 'App Deployment']),
            ],
            [
                'name'        => 'UI/UX Design',
                'slug'        => 'ui-ux-design',
                'category'    => 'Design',
                'description' => 'Design intuitive digital products with Figma, user research, and prototyping.',
                'fee'         => 300000,
                'weekly_outline' => json_encode(['Design Thinking', 'Wireframing', 'Figma Mastery', 'Usability Testing', 'Portfolio Project']),
            ],
            [
                'name'        => 'Cybersecurity Essentials',
                'slug'        => 'cybersecurity-essentials',
                'category'    => 'Security',
                'description' => 'Understand threats, secure systems, and start a career in cybersecurity.',
                'fee'         => 480000,
                'weekly_outline' => json_encode(['Networking Basics', 'Linux Security', 'Ethical Hacking', 'OWASP Top 10', 'CTF Challenges']),
            ],
            [
                'name'        => 'Cloud Computing with AWS',
                'slug'        => 'cloud-computing-aws',
                'category'    => 'Cloud',
                'description' => 'Deploy and manage scalable cloud infrastructure on Amazon Web Services.',
                'fee'         => 550000,
                'weekly_outline' => json_encode(['AWS Core Services', 'EC2 & S3', 'IAM & Security', 'Serverless', 'AWS Certification Prep']),
            ],
            [
                'name'        => 'Digital Marketing',
                'slug'        => 'digital-marketing',
                'category'    => 'Marketing',
                'description' => 'Drive growth with SEO, social media, content strategy, and paid ads.',
                'fee'         => 250000,
                'weekly_outline' => json_encode(['SEO & SEM', 'Social Media Strategy', 'Email Marketing', 'Google Analytics', 'Campaign Management']),
            ],
        ];

        $courseModels = [];
        foreach ($courses as $data) {
            $courseModels[] = Course::firstOrCreate(['slug' => $data['slug']], $data);
        }

        // ─────────────────────────────────────────────
        // 2. INSTRUCTORS  (statuses: active, pending, inactive)
        // ─────────────────────────────────────────────
        $instructors = [
            ['full_name' => 'Brian Otieno',    'email' => 'brian.otieno@devroots.ac.ug',    'phone' => '0701000001', 'expertise' => 'Web Development',  'experience_years' => 6,  'bio' => 'Full-stack developer with 6 years experience in Laravel and React.',   'status' => 'active'],
            ['full_name' => 'Amina Nakigozi',  'email' => 'amina.nakigozi@devroots.ac.ug',  'phone' => '0701000002', 'expertise' => 'Data Science',      'experience_years' => 4,  'bio' => 'Data scientist specialising in Python and machine learning.',          'status' => 'active'],
            ['full_name' => 'Samuel Mukasa',   'email' => 'samuel.mukasa@devroots.ac.ug',   'phone' => '0701000003', 'expertise' => 'Mobile Development', 'experience_years' => 5,  'bio' => 'Flutter engineer who has shipped 12 apps on both stores.',             'status' => 'active'],
            ['full_name' => 'Grace Apio',      'email' => 'grace.apio@devroots.ac.ug',      'phone' => '0701000004', 'expertise' => 'UI/UX Design',       'experience_years' => 3,  'bio' => 'Product designer with a background in human-computer interaction.',    'status' => 'pending'],
            ['full_name' => 'Moses Tumwine',   'email' => 'moses.tumwine@devroots.ac.ug',   'phone' => '0701000005', 'expertise' => 'Cybersecurity',      'experience_years' => 7,  'bio' => 'Certified ethical hacker and security consultant.',                   'status' => 'pending'],
            ['full_name' => 'Fatuma Nabirye',  'email' => 'fatuma.nabirye@devroots.ac.ug',  'phone' => '0701000006', 'expertise' => 'Cloud Computing',    'experience_years' => 4,  'bio' => 'AWS Solutions Architect with cloud migration experience.',             'status' => 'active'],
            ['full_name' => 'Joel Wasswa',     'email' => 'joel.wasswa@devroots.ac.ug',     'phone' => '0701000007', 'expertise' => 'Python Programming', 'experience_years' => 5,  'bio' => 'Backend engineer and open-source contributor.',                       'status' => 'inactive'],
        ];

        foreach ($instructors as $data) {
            Instructor::firstOrCreate(['email' => $data['email']], $data);
        }

        // ─────────────────────────────────────────────
        // 3. STUDENTS  (statuses: active, finished, pending)
        // ─────────────────────────────────────────────
        $students = [
            // Active students
            ['full_name' => 'Alice Namukasa',   'username' => 'alice_namukasa',   'email' => 'alice.namukasa@gmail.com',   'phone' => '0781100001', 'location' => 'Kampala',     'course_interest' => 'Web Development',   'goals' => 'Become a full-stack developer',        'agreed_terms' => true, 'status' => 'active',   'dob' => '2000-03-12'],
            ['full_name' => 'David Kato',        'username' => 'david_kato',        'email' => 'david.kato@gmail.com',       'phone' => '0781100002', 'location' => 'Entebbe',     'course_interest' => 'Python Programming', 'goals' => 'Automate business processes',          'agreed_terms' => true, 'status' => 'active',   'dob' => '1999-07-22'],
            ['full_name' => 'Priscilla Akello',  'username' => 'priscilla_akello',  'email' => 'priscilla.akello@gmail.com', 'phone' => '0781100003', 'location' => 'Jinja',       'course_interest' => 'Data Science',       'goals' => 'Work as a data analyst at a bank',     'agreed_terms' => true, 'status' => 'active',   'dob' => '2001-11-05'],
            ['full_name' => 'Emmanuel Ssempa',   'username' => 'emmanuel_ssempa',   'email' => 'emmanuel.ssempa@gmail.com',  'phone' => '0781100004', 'location' => 'Kampala',     'course_interest' => 'Mobile App Development', 'goals' => 'Launch my own startup app',       'agreed_terms' => true, 'status' => 'active',   'dob' => '1998-05-18'],
            ['full_name' => 'Ruth Nalumansi',    'username' => 'ruth_nalumansi',    'email' => 'ruth.nalumansi@gmail.com',   'phone' => '0781100005', 'location' => 'Mbarara',     'course_interest' => 'UI/UX Design',       'goals' => 'Design apps for African users',        'agreed_terms' => true, 'status' => 'active',   'dob' => '2002-01-30'],
            ['full_name' => 'Joseph Mugisha',    'username' => 'joseph_mugisha',    'email' => 'joseph.mugisha@gmail.com',   'phone' => '0781100006', 'location' => 'Gulu',        'course_interest' => 'Cybersecurity',      'goals' => 'Protect government systems',           'agreed_terms' => true, 'status' => 'active',   'dob' => '1997-09-14'],
            ['full_name' => 'Mary Atim',         'username' => 'mary_atim',         'email' => 'mary.atim@gmail.com',        'phone' => '0781100007', 'location' => 'Lira',        'course_interest' => 'Cloud Computing with AWS', 'goals' => 'Move into cloud engineering',   'agreed_terms' => true, 'status' => 'active',   'dob' => '2000-06-25'],
            ['full_name' => 'Ronald Byarugaba',  'username' => 'ronald_byarugaba',  'email' => 'ronald.byarugaba@gmail.com', 'phone' => '0781100008', 'location' => 'Fort Portal',  'course_interest' => 'Web Development',   'goals' => 'Build websites for local businesses',  'agreed_terms' => true, 'status' => 'active',   'dob' => '1999-12-02'],
            // Finished students
            ['full_name' => 'Sandra Nalwoga',    'username' => 'sandra_nalwoga',    'email' => 'sandra.nalwoga@gmail.com',   'phone' => '0781100009', 'location' => 'Kampala',     'course_interest' => 'Web Development',   'goals' => 'Freelance developer',                  'agreed_terms' => true, 'status' => 'finished', 'dob' => '1996-04-08'],
            ['full_name' => 'Ivan Kagwa',         'username' => 'ivan_kagwa',         'email' => 'ivan.kagwa@gmail.com',       'phone' => '0781100010', 'location' => 'Entebbe',     'course_interest' => 'Python Programming', 'goals' => 'Data engineer at a tech firm',         'agreed_terms' => true, 'status' => 'finished', 'dob' => '1995-08-16'],
            ['full_name' => 'Doreen Asiimwe',    'username' => 'doreen_asiimwe',    'email' => 'doreen.asiimwe@gmail.com',   'phone' => '0781100011', 'location' => 'Masaka',      'course_interest' => 'Digital Marketing',  'goals' => 'Run digital campaigns for NGOs',       'agreed_terms' => true, 'status' => 'finished', 'dob' => '1998-02-19'],
            ['full_name' => 'Patrick Nkurunziza','username' => 'patrick_nkurunziza','email' => 'patrick.nk@gmail.com',       'phone' => '0781100012', 'location' => 'Kampala',     'course_interest' => 'UI/UX Design',       'goals' => 'Lead UX at a product company',         'agreed_terms' => true, 'status' => 'finished', 'dob' => '1997-10-31'],
            // Pending students
            ['full_name' => 'Lydia Auma',         'username' => 'lydia_auma',         'email' => 'lydia.auma@gmail.com',       'phone' => '0781100013', 'location' => 'Soroti',      'course_interest' => 'Data Science',       'goals' => 'Apply ML in agriculture',              'agreed_terms' => true, 'status' => 'pending',  'dob' => '2003-03-07'],
            ['full_name' => 'Henry Okello',       'username' => 'henry_okello',       'email' => 'henry.okello@gmail.com',     'phone' => '0781100014', 'location' => 'Arua',        'course_interest' => 'Cybersecurity',      'goals' => 'Join the CERT Uganda team',            'agreed_terms' => true, 'status' => 'pending',  'dob' => '2002-07-21'],
            ['full_name' => 'Immaculate Tendo',  'username' => 'immaculate_tendo',   'email' => 'immaculate.tendo@gmail.com', 'phone' => '0781100015', 'location' => 'Kampala',     'course_interest' => 'Mobile App Development', 'goals' => 'Build health apps',              'agreed_terms' => true, 'status' => 'pending',  'dob' => '2001-09-13'],
        ];

        $studentModels = [];
        foreach ($students as $data) {
            $studentModels[] = Student::firstOrCreate(['email' => $data['email']], $data);
        }

        // ─────────────────────────────────────────────
        // 4. ENROLLMENTS — spread across all 12 months
        //    Statuses: pending, active, completed
        // ─────────────────────────────────────────────
        $enrollmentData = [
            // Jan
            ['student' => 0,  'course' => 0, 'status' => 'active',    'month' => 1],
            ['student' => 1,  'course' => 1, 'status' => 'active',    'month' => 1],
            ['student' => 2,  'course' => 2, 'status' => 'active',    'month' => 1],
            // Feb
            ['student' => 3,  'course' => 3, 'status' => 'active',    'month' => 2],
            ['student' => 4,  'course' => 4, 'status' => 'active',    'month' => 2],
            ['student' => 5,  'course' => 5, 'status' => 'active',    'month' => 2],
            // Mar
            ['student' => 6,  'course' => 6, 'status' => 'active',    'month' => 3],
            ['student' => 7,  'course' => 0, 'status' => 'active',    'month' => 3],
            ['student' => 8,  'course' => 0, 'status' => 'completed', 'month' => 3],
            // Apr
            ['student' => 9,  'course' => 1, 'status' => 'completed', 'month' => 4],
            ['student' => 10, 'course' => 7, 'status' => 'completed', 'month' => 4],
            ['student' => 11, 'course' => 4, 'status' => 'completed', 'month' => 4],
            // May
            ['student' => 0,  'course' => 2, 'status' => 'active',    'month' => 5],
            ['student' => 1,  'course' => 3, 'status' => 'active',    'month' => 5],
            // Jun
            ['student' => 2,  'course' => 5, 'status' => 'active',    'month' => 6],
            ['student' => 3,  'course' => 6, 'status' => 'active',    'month' => 6],
            ['student' => 4,  'course' => 7, 'status' => 'pending',   'month' => 6],
            // Jul
            ['student' => 5,  'course' => 0, 'status' => 'active',    'month' => 7],
            ['student' => 6,  'course' => 1, 'status' => 'active',    'month' => 7],
            // Aug
            ['student' => 7,  'course' => 2, 'status' => 'active',    'month' => 8],
            ['student' => 8,  'course' => 4, 'status' => 'completed', 'month' => 8],
            ['student' => 9,  'course' => 3, 'status' => 'active',    'month' => 8],
            // Sep
            ['student' => 10, 'course' => 5, 'status' => 'active',    'month' => 9],
            ['student' => 11, 'course' => 6, 'status' => 'completed', 'month' => 9],
            // Oct
            ['student' => 12, 'course' => 0, 'status' => 'pending',   'month' => 10],
            ['student' => 13, 'course' => 5, 'status' => 'pending',   'month' => 10],
            // Nov
            ['student' => 14, 'course' => 3, 'status' => 'pending',   'month' => 11],
            ['student' => 0,  'course' => 7, 'status' => 'active',    'month' => 11],
            ['student' => 1,  'course' => 6, 'status' => 'active',    'month' => 11],
            // Dec
            ['student' => 2,  'course' => 4, 'status' => 'active',    'month' => 12],
            ['student' => 3,  'course' => 2, 'status' => 'active',    'month' => 12],
        ];

        $enrollmentModels = [];
        foreach ($enrollmentData as $row) {
            $student = $studentModels[$row['student']];
            $course  = $courseModels[$row['course']];
            $date    = Carbon::create(2025, $row['month'], rand(1, 25));

            $enrollment = Enrollment::firstOrCreate(
                ['student_id' => $student->id, 'course_id' => $course->id],
                ['status' => $row['status'], 'created_at' => $date, 'updated_at' => $date]
            );
            $enrollmentModels[] = $enrollment;
        }

        // ─────────────────────────────────────────────
        // 5. PAYMENTS — methods: Mobile Money, Bank Transfer, Cash, Card
        //    Statuses: paid, pending, failed
        //    Spread across all 12 months with realistic amounts
        // ─────────────────────────────────────────────
        $methods  = ['Mobile Money', 'Bank Transfer', 'Cash', 'Card'];
        $paymentData = [
            // Jan
            ['student' => 0,  'course' => 0, 'amount' => 350000, 'method' => 'Mobile Money',  'status' => 'paid',    'month' => 1],
            ['student' => 1,  'course' => 1, 'amount' => 400000, 'method' => 'Bank Transfer',  'status' => 'paid',    'month' => 1],
            ['student' => 2,  'course' => 2, 'amount' => 250000, 'method' => 'Cash',            'status' => 'paid',    'month' => 1],
            // Feb
            ['student' => 3,  'course' => 3, 'amount' => 450000, 'method' => 'Mobile Money',  'status' => 'paid',    'month' => 2],
            ['student' => 4,  'course' => 4, 'amount' => 300000, 'method' => 'Card',            'status' => 'paid',    'month' => 2],
            // Mar
            ['student' => 5,  'course' => 5, 'amount' => 480000, 'method' => 'Bank Transfer',  'status' => 'paid',    'month' => 3],
            ['student' => 6,  'course' => 6, 'amount' => 550000, 'method' => 'Mobile Money',  'status' => 'paid',    'month' => 3],
            ['student' => 7,  'course' => 0, 'amount' => 175000, 'method' => 'Cash',            'status' => 'paid',    'month' => 3],
            // Apr
            ['student' => 8,  'course' => 0, 'amount' => 350000, 'method' => 'Mobile Money',  'status' => 'paid',    'month' => 4],
            ['student' => 9,  'course' => 1, 'amount' => 400000, 'method' => 'Card',            'status' => 'paid',    'month' => 4],
            ['student' => 10, 'course' => 7, 'amount' => 250000, 'method' => 'Bank Transfer',  'status' => 'paid',    'month' => 4],
            // May
            ['student' => 11, 'course' => 4, 'amount' => 300000, 'method' => 'Mobile Money',  'status' => 'paid',    'month' => 5],
            ['student' => 0,  'course' => 2, 'amount' => 500000, 'method' => 'Cash',            'status' => 'paid',    'month' => 5],
            // Jun
            ['student' => 1,  'course' => 3, 'amount' => 450000, 'method' => 'Mobile Money',  'status' => 'paid',    'month' => 6],
            ['student' => 2,  'course' => 5, 'amount' => 480000, 'method' => 'Bank Transfer',  'status' => 'paid',    'month' => 6],
            // Jul
            ['student' => 3,  'course' => 6, 'amount' => 550000, 'method' => 'Mobile Money',  'status' => 'paid',    'month' => 7],
            ['student' => 5,  'course' => 0, 'amount' => 350000, 'method' => 'Card',            'status' => 'paid',    'month' => 7],
            ['student' => 6,  'course' => 1, 'amount' => 200000, 'method' => 'Cash',            'status' => 'pending', 'month' => 7],
            // Aug
            ['student' => 7,  'course' => 2, 'amount' => 500000, 'method' => 'Bank Transfer',  'status' => 'paid',    'month' => 8],
            ['student' => 8,  'course' => 4, 'amount' => 300000, 'method' => 'Mobile Money',  'status' => 'paid',    'month' => 8],
            ['student' => 9,  'course' => 3, 'amount' => 450000, 'method' => 'Card',            'status' => 'paid',    'month' => 8],
            // Sep
            ['student' => 10, 'course' => 5, 'amount' => 480000, 'method' => 'Mobile Money',  'status' => 'paid',    'month' => 9],
            ['student' => 11, 'course' => 6, 'amount' => 550000, 'method' => 'Bank Transfer',  'status' => 'paid',    'month' => 9],
            // Oct
            ['student' => 12, 'course' => 0, 'amount' => 175000, 'method' => 'Mobile Money',  'status' => 'pending', 'month' => 10],
            ['student' => 13, 'course' => 5, 'amount' => 480000, 'method' => 'Cash',            'status' => 'failed',  'month' => 10],
            // Nov
            ['student' => 14, 'course' => 3, 'amount' => 450000, 'method' => 'Mobile Money',  'status' => 'pending', 'month' => 11],
            ['student' => 0,  'course' => 7, 'amount' => 250000, 'method' => 'Card',            'status' => 'paid',    'month' => 11],
            ['student' => 1,  'course' => 6, 'amount' => 550000, 'method' => 'Bank Transfer',  'status' => 'paid',    'month' => 11],
            // Dec
            ['student' => 2,  'course' => 4, 'amount' => 300000, 'method' => 'Mobile Money',  'status' => 'paid',    'month' => 12],
            ['student' => 3,  'course' => 2, 'amount' => 500000, 'method' => 'Mobile Money',  'status' => 'paid',    'month' => 12],
        ];

        foreach ($paymentData as $i => $row) {
            $student = $studentModels[$row['student']];
            $course  = $courseModels[$row['course']];
            $date    = Carbon::create(2025, $row['month'], rand(1, 25));
            $ref     = 'REF-' . strtoupper(substr(md5($i . $date), 0, 8));

            Payment::firstOrCreate(
                ['reference' => $ref],
                [
                    'student_id' => $student->id,
                    'course_id'  => $course->id,
                    'amount'     => $row['amount'],
                    'method'     => $row['method'],
                    'status'     => $row['status'],
                    'reference'  => $ref,
                    'paid_at'    => $row['status'] === 'paid' ? $date : null,
                    'created_at' => $date,
                    'updated_at' => $date,
                ]
            );
        }

        // ─────────────────────────────────────────────
        // 6. CHATS & MESSAGES
        // ─────────────────────────────────────────────
        $admin = User::where('is_admin', true)->first();

        if ($admin) {
            $chatUsers = User::where('is_admin', false)->take(3)->get();

            // Create 3 demo users for chat if needed
            if ($chatUsers->count() < 2) {
                $chatUsers = collect([
                    User::firstOrCreate(['email' => 'alice.chat@example.com'], [
                        'name' => 'Alice Namukasa', 'username' => 'alice_chat',
                        'password' => bcrypt('password'), 'is_admin' => false, 'role' => 'student',
                    ]),
                    User::firstOrCreate(['email' => 'david.chat@example.com'], [
                        'name' => 'David Kato', 'username' => 'david_chat',
                        'password' => bcrypt('password'), 'is_admin' => false, 'role' => 'student',
                    ]),
                ]);
            }

            $chatScenarios = [
                ['subject' => 'Course payment inquiry',   'status' => 'open',   'messages' => [
                    ['sender' => 'user',  'text' => 'Hello, I would like to know if I can pay in installments for the Web Development course.'],
                    ['sender' => 'admin', 'text' => 'Hi! Yes, we offer a 2-installment plan. First payment of 175,000 UGX on enrolment and the second midway through.'],
                    ['sender' => 'user',  'text' => 'That sounds great. How do I proceed?'],
                    ['sender' => 'admin', 'text' => 'Just head to the payments section and select "Installment". We will guide you from there.'],
                ]],
                ['subject' => 'Certificate request',      'status' => 'closed', 'messages' => [
                    ['sender' => 'user',  'text' => 'I completed the Python Programming course. When will I receive my certificate?'],
                    ['sender' => 'admin', 'text' => 'Congratulations! Certificates are emailed within 5 working days of course completion.'],
                    ['sender' => 'user',  'text' => 'Thank you!'],
                ]],
                ['subject' => 'Technical issue with LMS', 'status' => 'open',   'messages' => [
                    ['sender' => 'user',  'text' => 'I cannot access the course videos. The page keeps loading.'],
                    ['sender' => 'admin', 'text' => 'Sorry about that. Please try clearing your browser cache and let us know if the issue persists.'],
                ]],
            ];

            foreach ($chatScenarios as $idx => $scenario) {
                $chatUser = $chatUsers->get($idx % $chatUsers->count());
                if (!$chatUser) continue;

                $chat = Chat::firstOrCreate(
                    ['user_id' => $chatUser->id, 'subject' => $scenario['subject']],
                    ['admin_id' => $admin->id, 'status' => $scenario['status']]
                );

                foreach ($scenario['messages'] as $msg) {
                    $isAdmin = $msg['sender'] === 'admin';
                    Message::firstOrCreate(
                        ['chat_id' => $chat->id, 'message' => $msg['text']],
                        [
                            'sender_id' => $isAdmin ? $admin->id : $chatUser->id,
                            'is_admin'  => $isAdmin,
                        ]
                    );
                }
            }
        }
    }
}
