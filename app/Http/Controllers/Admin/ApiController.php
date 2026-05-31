<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\CaseAssigned;
use App\Mail\CaseClosed;
use App\Models\Account;
use App\Models\CaseNote;
use App\Models\Client;
use App\Models\FinancialPeriod;
use App\Models\LegalCase;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class ApiController extends Controller
{
    // ── Dashboard stats ────────────────────────────────────────

    public function dashboardStats(): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $q    = LegalCase::query();

        if ($user->isOfficer()) {
            $q->where(function ($sq) use ($user) {
                $sq->where('main_officer_id', $user->id)
                   ->orWhereHas('officers', fn($s2) => $s2->where('user_id', $user->id));
            });
        }

        $stats = [
            'total_cases'   => (clone $q)->count(),
            'active_cases'  => (clone $q)->whereIn('status', ['active', 'ongoing'])->count(),
            'pending_cases' => (clone $q)->where('status', 'pending')->count(),
            'closed_cases'  => (clone $q)->where('status', 'closed')->count(),
            'in_court'      => (clone $q)->where('is_in_court', true)->count(),
            'at_police'     => (clone $q)->where('is_at_police', true)->count(),
        ];

        if ($user->isAdmin()) {
            $stats['total_clients'] = Client::count();
            $stats['income_month']  = Transaction::where('type', 'income')
                ->whereMonth('transaction_date', now()->month)->whereYear('transaction_date', now()->year)->sum('amount');
            $stats['expense_month'] = Transaction::where('type', 'expense')
                ->whereMonth('transaction_date', now()->month)->whereYear('transaction_date', now()->year)->sum('amount');
            $stats['wins']   = LegalCase::where('score', 1)->count();
            $stats['losses'] = LegalCase::where('score', -1)->count();
        }

        return response()->json($stats);
    }

    // ── Cases list ─────────────────────────────────────────────

    public function cases(Request $request): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user  = Auth::user();
        $query = LegalCase::with(['client', 'mainOfficer']);

        if ($user->isOfficer()) {
            $query->where(function ($q) use ($user) {
                $q->where('main_officer_id', $user->id)
                  ->orWhereHas('officers', fn($q2) => $q2->where('user_id', $user->id));
            });
        }

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%$search%")
                  ->orWhere('case_number', 'like', "%$search%")
                  ->orWhereHas('client', fn($q2) =>
                      $q2->where('first_name', 'like', "%$search%")->orWhere('last_name', 'like', "%$search%"));
            });
        }

        if ($v = $request->get('status'))   $query->where('status', $v);
        if ($v = $request->get('category')) $query->where('category', $v);
        if ($v = $request->get('priority')) $query->where('priority', $v);

        $cases = $query->latest()->paginate(25);

        return response()->json([
            'data' => $cases->map(fn($c) => [
                'id'             => $c->id,
                'case_number'    => $c->case_number,
                'title'          => $c->title,
                'status'         => $c->status,
                'stage'          => $c->stage,
                'priority'       => $c->priority,
                'category'       => $c->category,
                'category_label' => LegalCase::categoryLabel($c->category),
                'client'         => $c->client ? ['id' => $c->client->id, 'full_name' => $c->client->full_name] : null,
                'main_officer'   => $c->mainOfficer ? ['name' => $c->mainOfficer->name] : null,
                'is_in_court'    => $c->is_in_court,
                'is_at_police'   => $c->is_at_police,
                'filing_date'    => $c->filing_date->format('d M Y'),
                'score'          => $c->score,
            ]),
            'meta' => [
                'total'        => $cases->total(),
                'current_page' => $cases->currentPage(),
                'last_page'    => $cases->lastPage(),
                'per_page'     => $cases->perPage(),
                'from'         => $cases->firstItem(),
                'to'           => $cases->lastItem(),
            ],
        ]);
    }

    // ── Case detail (for drawer) ───────────────────────────────

    public function caseDetail(LegalCase $case): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $case->load(['client', 'mainOfficer', 'officers', 'notes.author', 'documents', 'transactions.account']);

        return response()->json([
            'id'                    => $case->id,
            'case_number'           => $case->case_number,
            'title'                 => $case->title,
            'description'           => $case->description,
            'status'                => $case->status,
            'stage'                 => $case->stage,
            'stage_label'           => LegalCase::stageLabel($case->stage),
            'priority'              => $case->priority,
            'category'              => $case->category,
            'category_label'        => LegalCase::categoryLabel($case->category),
            'filing_date'           => $case->filing_date->format('d M Y'),
            'closed_date'           => $case->closed_date?->format('d M Y'),
            'days_open'             => $case->filing_date->diffInDays(now()),
            'score'                 => $case->score,
            'score_label'           => $case->score_label,
            'closing_remarks'       => $case->closing_remarks,
            'client'                => $case->client ? [
                'id'            => $case->client->id,
                'full_name'     => $case->client->full_name,
                'client_number' => $case->client->client_number,
                'phone'         => $case->client->phone,
                'email'         => $case->client->email,
            ] : null,
            'main_officer'          => $case->mainOfficer ? ['id' => $case->mainOfficer->id, 'name' => $case->mainOfficer->name] : null,
            'officers'              => $case->officers->map(fn($o) => [
                'id'   => $o->id,
                'name' => $o->name,
                'role' => $o->pivot->role,
            ]),
            'is_in_court'           => $case->is_in_court,
            'court_name'            => $case->court_name,
            'court_division'        => $case->court_division,
            'court_case_number'     => $case->court_case_number,
            'judge_name'            => $case->judge_name,
            'next_hearing_date'     => $case->next_hearing_date?->format('d M Y'),
            'is_at_police'          => $case->is_at_police,
            'police_station'        => $case->police_station,
            'police_ref_number'     => $case->police_ref_number,
            'investigating_officer' => $case->investigating_officer,
            'notes'                 => $case->notes->map(fn($note) => [
                'id'         => $note->id,
                'note'       => $note->note,
                'is_private' => $note->is_private,
                'author'     => $note->author?->name ?? 'Unknown',
                'initials'   => $note->author?->initials ?? strtoupper(substr($note->author?->name ?? 'U', 0, 2)),
                'avatar_url' => $note->author?->avatar_url,
                'created_at' => $note->created_at->format('d M Y, H:i'),
                'diff'       => $note->created_at->diffForHumans(),
                'can_delete' => $user->id === $note->user_id || $user->isAdmin(),
            ]),
            'documents'             => $case->documents->map(fn($d) => [
                'id'           => $d->id,
                'doc_number'   => $d->doc_number,
                'title'        => $d->title,
                'category'     => \App\Models\Document::categoryLabel($d->category),
                'download_url' => route('admin.documents.download', $d),
                'show_url'     => route('admin.documents.show', $d),
            ]),
            'transactions'          => $case->transactions->take(5)->map(fn($t) => [
                'id'          => $t->id,
                'type'        => $t->type,
                'amount'      => number_format($t->amount, 0),
                'description' => $t->description,
                'date'        => $t->transaction_date->format('d M Y'),
                'account'     => $t->account?->name,
            ]),
            'can_close'             => !in_array($case->status, ['closed', 'archived']),
            'edit_url'              => route('admin.cases.edit', $case),
            'show_url'              => route('admin.cases.show', $case),
        ]);
    }

    // ── Case mutations ─────────────────────────────────────────

    public function addNote(Request $request, LegalCase $case): JsonResponse
    {
        $data = $request->validate([
            'note'       => 'required|string|max:2000',
            'is_private' => 'boolean',
        ]);

        $note = $case->notes()->create([
            'user_id'    => Auth::id(),
            'note'       => $data['note'],
            'is_private' => $request->boolean('is_private'),
        ]);
        $note->load('author');

        return response()->json([
            'id'         => $note->id,
            'note'       => $note->note,
            'is_private' => $note->is_private,
            'author'     => $note->author?->name,
            'initials'   => $note->author?->initials ?? strtoupper(substr($note->author?->name ?? 'U', 0, 2)),
            'avatar_url' => $note->author?->avatar_url,
            'created_at' => $note->created_at->format('d M Y, H:i'),
            'diff'       => $note->created_at->diffForHumans(),
            'can_delete' => true,
        ], 201);
    }

    public function deleteNote(CaseNote $note): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if ($note->user_id !== $user->id && !$user->isAdmin()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $note->delete();
        return response()->json(['success' => true]);
    }

    public function updateCaseStatus(Request $request, LegalCase $case): JsonResponse
    {
        $data = $request->validate([
            'status' => 'required|in:pending,active,ongoing,closed,archived',
            'stage'  => 'nullable|in:intake,investigation,pre_trial,mediation,trial,appeal,settlement,enforcement,closed',
        ]);
        $case->update(array_filter($data, fn($v) => $v !== null));
        return response()->json(['success' => true, 'status' => $case->status]);
    }

    public function closeCase(Request $request, LegalCase $case): JsonResponse
    {
        $data = $request->validate([
            'score'           => 'required|in:1,0,-1',
            'closing_remarks' => 'nullable|string',
        ]);
        $case->update([
            'status'          => 'closed',
            'stage'           => 'closed',
            'score'           => (int) $data['score'],
            'closing_remarks' => $data['closing_remarks'] ?? null,
            'closed_date'     => now()->toDateString(),
        ]);

        // Notify all case officers
        $case->load('officers');
        $notified = collect();
        if ($case->main_officer_id) {
            $notified->push($case->main_officer_id);
            $this->mailClosed($case->main_officer_id, $case);
        }
        foreach ($case->officers as $officer) {
            if (!$notified->contains($officer->id)) {
                $this->mailClosed($officer->id, $case);
            }
        }

        return response()->json(['success' => true]);
    }

    public function reopenCase(LegalCase $case): JsonResponse
    {
        $case->update(['status' => 'ongoing', 'stage' => 'trial', 'score' => null, 'closed_date' => null]);
        if ($case->main_officer_id) {
            $this->mailAssignment($case->main_officer_id, $case, 'main');
        }
        return response()->json(['success' => true]);
    }

    public function quickCreateCase(Request $request): JsonResponse
    {
        $data = $request->validate([
            'title'           => 'required|string|max:200',
            'category'        => 'required|string',
            'priority'        => 'required|in:low,medium,high,urgent',
            'client_id'       => 'required|exists:clients,id',
            'main_officer_id' => 'nullable|exists:users,id',
            'filing_date'     => 'required|date',
            'description'     => 'nullable|string',
        ]);

        $data['case_number'] = LegalCase::generateNumber();
        $data['status']      = 'pending';
        $data['stage']       = 'intake';
        $data['created_by']  = Auth::id();

        $case = LegalCase::create($data);
        $case->load('client');

        // Notify assigned officer
        if (!empty($data['main_officer_id'])) {
            $this->mailAssignment($data['main_officer_id'], $case, 'main');
        }

        return response()->json([
            'success'      => true,
            'id'           => $case->id,
            'case_number'  => $case->case_number,
            'title'        => $case->title,
            'status'       => $case->status,
            'client'       => $case->client?->full_name,
        ], 201);
    }

    // ── Clients ────────────────────────────────────────────────

    public function clients(Request $request): JsonResponse
    {
        $query = Client::withCount('cases');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%$search%")
                  ->orWhere('last_name', 'like', "%$search%")
                  ->orWhere('client_number', 'like', "%$search%")
                  ->orWhere('phone', 'like', "%$search%");
            });
        }

        $clients = $query->latest()->paginate(25);

        return response()->json([
            'data' => $clients->map(fn($c) => [
                'id'            => $c->id,
                'client_number' => $c->client_number,
                'full_name'     => $c->full_name,
                'phone'         => $c->phone,
                'email'         => $c->email,
                'district'      => $c->district,
                'company'       => $c->company,
                'cases_count'   => $c->cases_count,
                'initials'      => strtoupper(substr($c->first_name, 0, 1) . substr($c->last_name, 0, 1)),
                'created_at'    => $c->created_at->format('d M Y'),
            ]),
            'meta' => ['total' => $clients->total(), 'current_page' => $clients->currentPage(), 'last_page' => $clients->lastPage()],
        ]);
    }

    public function clientDetail(Client $client): JsonResponse
    {
        $client->load(['cases.mainOfficer', 'createdBy']);

        return response()->json([
            'id'            => $client->id,
            'client_number' => $client->client_number,
            'full_name'     => $client->full_name,
            'initials'      => strtoupper(substr($client->first_name, 0, 1) . substr($client->last_name, 0, 1)),
            'email'         => $client->email,
            'phone'         => $client->phone,
            'phone_alt'     => $client->phone_alt,
            'gender'        => $client->gender ? ucfirst($client->gender) : null,
            'dob'           => $client->dob?->format('d M Y'),
            'id_type'       => $client->id_type ? ucwords(str_replace('_', ' ', $client->id_type)) : null,
            'id_number'     => $client->id_number,
            'address'       => $client->address,
            'district'      => $client->district,
            'occupation'    => $client->occupation,
            'company'       => $client->company,
            'notes'         => $client->notes,
            'registered'    => $client->created_at->format('d M Y'),
            'cases'         => $client->cases->map(fn($case) => [
                'id'          => $case->id,
                'case_number' => $case->case_number,
                'title'       => $case->title,
                'status'      => $case->status,
                'officer'     => $case->mainOfficer?->name,
            ]),
            'edit_url'     => route('admin.clients.edit', $client),
        ]);
    }

    public function quickCreateClient(Request $request): JsonResponse
    {
        $data = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name'  => 'required|string|max:100',
            'phone'      => 'required|string|max:20',
            'email'      => 'nullable|email|max:150',
            'address'    => 'required|string',
            'district'   => 'nullable|string|max:100',
        ]);

        $data['client_number'] = Client::generateNumber();
        $data['created_by']    = Auth::id();

        $client = Client::create($data);

        return response()->json([
            'success'       => true,
            'id'            => $client->id,
            'client_number' => $client->client_number,
            'full_name'     => $client->full_name,
            'phone'         => $client->phone,
        ], 201);
    }

    // ── Clients select (for Select2 AJAX) ─────────────────────

    public function clientsSelect(Request $request): JsonResponse
    {
        $clients = Client::when($request->get('q'), fn($q, $s) =>
                $q->where('first_name', 'like', "%$s%")->orWhere('last_name', 'like', "%$s%")->orWhere('client_number', 'like', "%$s%")
            )
            ->orderBy('first_name')
            ->limit(30)
            ->get(['id', 'first_name', 'last_name', 'client_number', 'phone']);

        return response()->json([
            'results' => $clients->map(fn($c) => [
                'id'   => $c->id,
                'text' => "{$c->full_name} ({$c->client_number})",
                'phone' => $c->phone,
            ])
        ]);
    }

    // ── Quick record transaction ───────────────────────────────

    public function quickCreateTransaction(Request $request): JsonResponse
    {
        $data = $request->validate([
            'type'                => 'required|in:income,expense',
            'amount'              => 'required|numeric|min:0.01',
            'description'         => 'required|string|max:255',
            'account_id'          => 'required|exists:accounts,id',
            'payment_method'      => 'required|in:cash,bank_transfer,cheque,mobile_money',
            'transaction_date'    => 'required|date',
            'case_id'             => 'nullable|exists:legal_cases,id',
            'client_id'           => 'nullable|exists:clients,id',
            'financial_period_id' => 'nullable|exists:financial_periods,id',
            'reference_number'    => 'nullable|string|max:100',
        ]);

        $data['transaction_number'] = Transaction::generateNumber();
        $data['created_by']         = Auth::id();
        if ($data['type'] === 'income') {
            $data['receipt_number'] = Transaction::generateReceiptNumber();
        }

        $txn = Transaction::create($data);

        return response()->json([
            'success'            => true,
            'transaction_number' => $txn->transaction_number,
            'receipt_number'     => $txn->receipt_number,
            'amount'             => number_format($txn->amount, 0),
            'type'               => $txn->type,
            'show_url'           => route('admin.transactions.show', $txn),
        ], 201);
    }

    // ── Lookup data (for dropdowns) ────────────────────────────

    public function officers(): JsonResponse
    {
        $officers = User::whereIn('role', ['admin', 'officer'])
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'role']);

        return response()->json($officers->map(fn($u) => [
            'id'   => $u->id,
            'name' => $u->name,
            'role' => $u->role_label,
        ]));
    }

    public function accounts(): JsonResponse
    {
        $accounts = Account::where('is_active', true)->orderBy('name')->get(['id', 'name', 'type']);
        return response()->json($accounts->map(fn($a) => [
            'id'   => $a->id,
            'name' => $a->name,
            'type' => \App\Models\Account::typeLabel($a->type),
        ]));
    }

    public function activePeriod(): JsonResponse
    {
        $period = FinancialPeriod::where('is_active', true)->first(['id', 'name']);
        return response()->json($period ? ['id' => $period->id, 'name' => $period->name] : null);
    }

    public function activeCases(Request $request): JsonResponse
    {
        $cases = LegalCase::with('client')
            ->whereIn('status', ['active', 'ongoing', 'pending'])
            ->orderBy('case_number')
            ->get(['id', 'case_number', 'title', 'client_id']);

        return response()->json($cases->map(fn($c) => [
            'id'   => $c->id,
            'text' => "{$c->case_number} — {$c->client?->full_name}",
        ]));
    }

    // ── Documents ──────────────────────────────────────────────

    public function uploadDocument(Request $request): JsonResponse
    {
        $request->validate([
            'file'            => 'required|file|max:20480',
            'title'           => 'required|string|max:200',
            'category'        => 'required|string',
            'case_id'         => 'nullable|exists:legal_cases,id',
            'client_id'       => 'nullable|exists:clients,id',
            'description'     => 'nullable|string',
            'is_confidential' => 'nullable|boolean',
        ]);

        $file      = $request->file('file');
        $path      = $file->store('documents', 'public');
        $docNumber = \App\Models\Document::generateNumber();

        $doc = \App\Models\Document::create([
            'doc_number'      => $docNumber,
            'title'           => $request->title,
            'category'        => $request->category,
            'case_id'         => $request->case_id ?: null,
            'client_id'       => $request->client_id ?: null,
            'description'     => $request->description,
            'is_confidential' => $request->boolean('is_confidential'),
            'file_path'       => $path,
            'file_name'       => $file->getClientOriginalName(),
            'file_size'       => $file->getSize(),
            'mime_type'       => $file->getMimeType(),
            'uploaded_by'     => Auth::id(),
        ]);

        return response()->json([
            'success'       => true,
            'id'            => $doc->id,
            'doc_number'    => $doc->doc_number,
            'title'         => $doc->title,
            'category'      => \App\Models\Document::categoryLabel($doc->category),
            'file_name'     => $doc->file_name,
            'file_size'     => $doc->file_size_formatted,
            'is_confidential' => $doc->is_confidential,
            'download_url'  => route('admin.documents.download', $doc),
            'show_url'      => route('admin.documents.show', $doc),
        ], 201);
    }

    public function deleteDocument(\App\Models\Document $document): JsonResponse
    {
        \Illuminate\Support\Facades\Storage::disk('public')->delete($document->file_path);
        $document->delete();
        return response()->json(['success' => true]);
    }

    public function caseDocuments(LegalCase $case): JsonResponse
    {
        $docs = $case->documents()->with('uploader')->latest()->get();
        return response()->json($docs->map(fn($d) => [
            'id'           => $d->id,
            'doc_number'   => $d->doc_number,
            'title'        => $d->title,
            'category'     => \App\Models\Document::categoryLabel($d->category),
            'file_name'    => $d->file_name,
            'file_size'    => $d->file_size_formatted,
            'is_confidential' => $d->is_confidential,
            'uploaded_by'  => $d->uploader?->name,
            'created_at'   => $d->created_at->format('d M Y'),
            'download_url' => route('admin.documents.download', $d),
            'show_url'     => route('admin.documents.show', $d),
        ]));
    }

    // ── Recent Activity ────────────────────────────────────────

    public function recentActivity(): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $items = collect();

        // Recent cases
        $cq = LegalCase::with('client')->latest()->take(5);
        if ($user->isOfficer()) {
            $cq->where(fn($q) => $q->where('main_officer_id', $user->id)
                ->orWhereHas('officers', fn($q2) => $q2->where('user_id', $user->id)));
        }
        $cq->get()->each(fn($c) => $items->push([
            'type'  => 'case',
            'icon'  => 'fa-scale-balanced',
            'text'  => "Case <strong>{$c->case_number}</strong> — {$c->title}",
            'sub'   => $c->client?->full_name . ' · ' . ucfirst($c->status),
            'time'  => $c->created_at->diffForHumans(),
            'url'   => route('admin.cases.show', $c),
        ]));

        if ($user->isAdmin() || $user->isFrontdesk()) {
            // Recent documents
            \App\Models\Document::with('uploader')->latest()->take(4)->get()->each(fn($d) => $items->push([
                'type'  => 'doc',
                'icon'  => 'fa-file-alt',
                'text'  => "Document <strong>{$d->doc_number}</strong> — {$d->title}",
                'sub'   => \App\Models\Document::categoryLabel($d->category),
                'time'  => $d->created_at->diffForHumans(),
                'url'   => route('admin.documents.show', $d),
            ]));

            // Recent transactions
            Transaction::latest()->take(4)->get()->each(fn($t) => $items->push([
                'type'  => 'txn',
                'icon'  => 'fa-money-bill-transfer',
                'text'  => ucfirst($t->type) . ' <strong>UGX ' . number_format($t->amount, 0) . '</strong>',
                'sub'   => $t->description . ' · ' . $t->transaction_date->format('d M Y'),
                'time'  => $t->created_at->diffForHumans(),
                'url'   => route('admin.transactions.show', $t),
            ]));
        }

        return response()->json(
            $items->sortByDesc(fn($i) => $i['time'])->take(12)->values()
        );
    }

    // ── Self-profile management ────────────────────────────────

    public function profileUpdate(Request $request): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $data = $request->validate([
            'name'  => 'required|string|max:150',
            'email' => "required|email|unique:users,email,{$user->id}",
            'phone' => 'nullable|string|max:20',
            'bio'   => 'nullable|string|max:500',
        ]);

        $user->update($data);

        return response()->json([
            'success'    => true,
            'name'       => $user->name,
            'email'      => $user->email,
            'avatar_url' => $user->avatar_url,
            'initials'   => $user->initials,
        ]);
    }

    public function profileAvatarUpdate(Request $request): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $request->validate([
            'avatar' => 'required|image|max:10240|mimes:jpg,jpeg,png,webp,gif',
        ]);

        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        $path = $request->file('avatar')->store('avatars', 'public');
        $user->update(['avatar' => $path]);

        return response()->json([
            'success'    => true,
            'avatar_url' => $user->avatar_url,
            'initials'   => $user->initials,
        ]);
    }

    public function profileAvatarRemove(): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }
        $user->update(['avatar' => null]);

        return response()->json(['success' => true, 'initials' => $user->initials]);
    }

    public function profilePasswordChange(Request $request): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $request->validate([
            'current_password'      => 'required|string',
            'password'              => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required|string',
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'message' => 'The current password is incorrect.',
                'errors'  => ['current_password' => ['The current password is incorrect.']],
            ], 422);
        }

        $user->update(['password' => Hash::make($request->password)]);

        return response()->json(['success' => true]);
    }

    // ── Mail helpers ───────────────────────────────────────────

    private function mailAssignment(int $officerId, LegalCase $case, string $role): void
    {
        $officer = User::find($officerId);
        if (!$officer?->email) return;
        try {
            Mail::to($officer->email)->send(new CaseAssigned($officer, $case, $role));
        } catch (\Exception $e) {
            \Log::error("CaseAssigned mail failed [{$case->case_number}] → {$officer->email}: " . $e->getMessage());
        }
    }

    private function mailClosed(int $officerId, LegalCase $case): void
    {
        $officer = User::find($officerId);
        if (!$officer?->email) return;
        try {
            Mail::to($officer->email)->send(new CaseClosed($officer, $case));
        } catch (\Exception $e) {
            \Log::error("CaseClosed mail failed [{$case->case_number}] → {$officer->email}: " . $e->getMessage());
        }
    }
}
