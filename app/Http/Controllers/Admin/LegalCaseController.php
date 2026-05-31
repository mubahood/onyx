<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\CaseAssigned;
use App\Mail\CaseClosed;
use App\Models\LegalCase;
use App\Models\CaseNote;
use App\Models\Client;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class LegalCaseController extends Controller
{
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

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
                  ->orWhereHas('client', fn($q2) => $q2->where('first_name', 'like', "%$search%")
                      ->orWhere('last_name', 'like', "%$search%"));
            });
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        if ($category = $request->get('category')) {
            $query->where('category', $category);
        }

        $cases = $query->latest()->paginate(20);

        // Stat chip counts (scoped to the same user filter)
        $baseQ = LegalCase::query();
        if ($user->isOfficer()) {
            $baseQ->where(function ($q) use ($user) {
                $q->where('main_officer_id', $user->id)
                  ->orWhereHas('officers', fn($q2) => $q2->where('user_id', $user->id));
            });
        }
        $allCount     = (clone $baseQ)->count();
        $pendingCount = (clone $baseQ)->where('status', 'pending')->count();
        $activeCount  = (clone $baseQ)->where('status', 'active')->count();
        $ongoingCount = (clone $baseQ)->where('status', 'ongoing')->count();
        $closedCount  = (clone $baseQ)->whereIn('status', ['closed', 'archived'])->count();

        return view('admin.cases.index', compact(
            'cases', 'allCount', 'pendingCount', 'activeCount', 'ongoingCount', 'closedCount'
        ));
    }

    public function create()
    {
        $clients  = Client::orderBy('first_name')->get();
        $officers = User::whereIn('role', ['admin', 'officer'])->where('is_active', true)->orderBy('name')->get();

        return view('admin.cases.create', compact('clients', 'officers'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'              => 'required|string|max:200',
            'description'        => 'nullable|string',
            'category'           => 'required|in:civil_litigation,criminal_defense,family_law,land_property,commercial_corporate,employment_labour,human_rights,constitutional,succession_probate,debt_recovery,immigration,other',
            'priority'           => 'required|in:low,medium,high,urgent',
            'client_id'          => 'required|exists:clients,id',
            'main_officer_id'    => 'nullable|exists:users,id',
            'filing_date'        => 'required|date',
            'is_in_court'        => 'boolean',
            'court_name'         => 'nullable|string|max:200',
            'court_division'     => 'nullable|string|max:200',
            'court_case_number'  => 'nullable|string|max:100',
            'judge_name'         => 'nullable|string|max:150',
            'next_hearing_date'  => 'nullable|date',
            'is_at_police'       => 'boolean',
            'police_station'     => 'nullable|string|max:200',
            'police_ref_number'  => 'nullable|string|max:100',
            'investigating_officer' => 'nullable|string|max:150',
            'team_officers'      => 'nullable|array',
            'team_officers.*'    => 'exists:users,id',
        ]);

        $data['case_number'] = LegalCase::generateNumber();
        $data['status']      = 'pending';
        $data['stage']       = 'intake';
        $data['created_by']  = Auth::id();
        $data['is_in_court'] = $request->boolean('is_in_court');
        $data['is_at_police']= $request->boolean('is_at_police');

        $case = LegalCase::create($data);

        // Attach team officers
        if (!empty($data['team_officers'])) {
            foreach ($data['team_officers'] as $officerId) {
                if ($officerId != $data['main_officer_id']) {
                    $case->officers()->attach($officerId, ['role' => 'team']);
                    $this->sendAssignmentEmail($officerId, $case, 'team');
                }
            }
        }
        if ($data['main_officer_id']) {
            $case->officers()->syncWithoutDetaching([$data['main_officer_id'] => ['role' => 'main']]);
            $this->sendAssignmentEmail($data['main_officer_id'], $case, 'main');
        }

        return redirect()->route('admin.cases.show', $case)
            ->with('success', "Case {$case->case_number} created successfully.");
    }

    public function show(LegalCase $case)
    {
        $case->load(['client', 'mainOfficer', 'officers', 'notes.author', 'documents', 'transactions.account']);

        return view('admin.cases.show', compact('case'));
    }

    public function edit(LegalCase $case)
    {
        $clients  = Client::orderBy('first_name')->get();
        $officers = User::whereIn('role', ['admin', 'officer'])->where('is_active', true)->orderBy('name')->get();
        $teamIds  = $case->officers()->wherePivot('role', 'team')->pluck('users.id')->toArray();

        return view('admin.cases.edit', compact('case', 'clients', 'officers', 'teamIds'));
    }

    public function update(Request $request, LegalCase $case)
    {
        $data = $request->validate([
            'title'                 => 'required|string|max:200',
            'description'           => 'nullable|string',
            'category'              => 'required|in:civil_litigation,criminal_defense,family_law,land_property,commercial_corporate,employment_labour,human_rights,constitutional,succession_probate,debt_recovery,immigration,other',
            'status'                => 'required|in:pending,active,ongoing,closed,archived',
            'stage'                 => 'required|in:intake,investigation,pre_trial,mediation,trial,appeal,settlement,enforcement,closed',
            'priority'              => 'required|in:low,medium,high,urgent',
            'client_id'             => 'required|exists:clients,id',
            'main_officer_id'       => 'nullable|exists:users,id',
            'filing_date'           => 'required|date',
            'is_in_court'           => 'boolean',
            'court_name'            => 'nullable|string|max:200',
            'court_division'        => 'nullable|string|max:200',
            'court_case_number'     => 'nullable|string|max:100',
            'judge_name'            => 'nullable|string|max:150',
            'next_hearing_date'     => 'nullable|date',
            'is_at_police'          => 'boolean',
            'police_station'        => 'nullable|string|max:200',
            'police_ref_number'     => 'nullable|string|max:100',
            'investigating_officer' => 'nullable|string|max:150',
            'team_officers'         => 'nullable|array',
            'team_officers.*'       => 'exists:users,id',
        ]);

        $data['is_in_court']  = $request->boolean('is_in_court');
        $data['is_at_police'] = $request->boolean('is_at_police');

        $prevMainOfficerId = $case->main_officer_id;
        $prevTeamIds       = $case->officers()->wherePivot('role', 'team')->pluck('users.id')->toArray();

        $case->update($data);

        // Sync team officers and detect newly added members
        $newTeamIds   = collect($data['team_officers'] ?? [])
            ->filter(fn($id) => $id != $data['main_officer_id'])
            ->values()->toArray();

        $teamOfficers = collect($newTeamIds)
            ->mapWithKeys(fn($id) => [$id => ['role' => 'team']])
            ->toArray();

        if ($data['main_officer_id']) {
            $teamOfficers[$data['main_officer_id']] = ['role' => 'main'];
        }
        $case->officers()->sync($teamOfficers);

        // Email newly assigned main officer
        if ($data['main_officer_id'] && $data['main_officer_id'] != $prevMainOfficerId) {
            $this->sendAssignmentEmail($data['main_officer_id'], $case, 'main');
        }
        // Email newly added team members
        foreach (array_diff($newTeamIds, $prevTeamIds) as $newId) {
            $this->sendAssignmentEmail($newId, $case, 'team');
        }

        return redirect()->route('admin.cases.show', $case)
            ->with('success', 'Case updated successfully.');
    }

    public function destroy(LegalCase $case)
    {
        $number = $case->case_number;
        $case->delete();

        return redirect()->route('admin.cases.index')
            ->with('success', "Case {$number} deleted.");
    }

    // ── Notes ──────────────────────────────────────────────────

    public function storeNote(Request $request, LegalCase $case)
    {
        $data = $request->validate([
            'note'       => 'required|string',
            'is_private' => 'boolean',
        ]);

        $case->notes()->create([
            'user_id'    => Auth::id(),
            'note'       => $data['note'],
            'is_private' => $request->boolean('is_private'),
        ]);

        return back()->with('success', 'Note added.');
    }

    public function destroyNote(LegalCase $case, CaseNote $note)
    {
        $note->delete();

        return back()->with('success', 'Note deleted.');
    }

    // ── Status transitions ─────────────────────────────────────

    public function close(Request $request, LegalCase $case)
    {
        $data = $request->validate([
            'score'           => 'required|in:1,0,-1',
            'closing_remarks' => 'nullable|string',
        ]);

        $case->update([
            'status'          => 'closed',
            'stage'           => 'closed',
            'score'           => (int) $data['score'],
            'closing_remarks' => $data['closing_remarks'],
            'closed_date'     => now()->toDateString(),
        ]);

        // Notify all officers assigned to this case
        $case->load('officers');
        $notified = collect();
        if ($case->main_officer_id) {
            $notified->push($case->main_officer_id);
            $this->sendClosedEmail($case->main_officer_id, $case);
        }
        foreach ($case->officers as $officer) {
            if (!$notified->contains($officer->id)) {
                $this->sendClosedEmail($officer->id, $case);
            }
        }

        return back()->with('success', 'Case closed and outcome recorded.');
    }

    public function reopen(LegalCase $case)
    {
        $case->update([
            'status'      => 'ongoing',
            'stage'       => 'trial',
            'score'       => null,
            'closed_date' => null,
        ]);

        // Notify main officer the case has been reopened
        if ($case->main_officer_id) {
            $this->sendAssignmentEmail($case->main_officer_id, $case, 'main');
        }

        return back()->with('success', 'Case reopened.');
    }

    // ── Email helpers ──────────────────────────────────────────

    private function sendAssignmentEmail(int $officerId, LegalCase $case, string $role): void
    {
        $officer = User::find($officerId);
        if (!$officer || !$officer->email) {
            return;
        }
        try {
            Mail::to($officer->email)->send(new CaseAssigned($officer, $case, $role));
        } catch (\Exception $e) {
            \Log::error("CaseAssigned email failed [{$case->case_number}] to {$officer->email}: " . $e->getMessage());
        }
    }

    private function sendClosedEmail(int $officerId, LegalCase $case): void
    {
        $officer = User::find($officerId);
        if (!$officer || !$officer->email) {
            return;
        }
        try {
            Mail::to($officer->email)->send(new CaseClosed($officer, $case));
        } catch (\Exception $e) {
            \Log::error("CaseClosed email failed [{$case->case_number}] to {$officer->email}: " . $e->getMessage());
        }
    }
}
