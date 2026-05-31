<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\LegalCaseController;
use App\Http\Controllers\Admin\DocumentController;
use App\Http\Controllers\Admin\AccountController;
use App\Http\Controllers\Admin\FinancialPeriodController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SettingController;

// ── Public ─────────────────────────────────────────────────────────────────
Route::get('/', fn() => redirect()->route('admin.login'));

// ── Admin Auth ──────────────────────────────────────────────────────────────
Route::get('/admin/login',  fn() => view('auth.admin-login'))->name('admin.login');
Route::post('/admin/login', [\App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'store']);
Route::post('/admin/logout', [\App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'destroy'])->name('admin.logout');
Route::get('/login', fn() => redirect()->route('admin.login'));

// ── Admin Protected ─────────────────────────────────────────────────────────
Route::prefix('admin')->middleware(['auth', 'admin'])->name('admin.')->group(function () {

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // ── Legal ──────────────────────────────────────────────────
    Route::resource('clients', ClientController::class);

    Route::resource('cases', LegalCaseController::class);
    Route::post('cases/{case}/notes', [LegalCaseController::class, 'storeNote'])->name('cases.notes.store');
    Route::delete('cases/{case}/notes/{note}', [LegalCaseController::class, 'destroyNote'])->name('cases.notes.destroy');
    Route::post('cases/{case}/close', [LegalCaseController::class, 'close'])->name('cases.close');
    Route::post('cases/{case}/reopen', [LegalCaseController::class, 'reopen'])->name('cases.reopen');

    Route::resource('documents', DocumentController::class);
    Route::get('documents/{document}/download', [DocumentController::class, 'download'])->name('documents.download');

    // ── Finance ────────────────────────────────────────────────
    Route::resource('accounts', AccountController::class);
    Route::resource('periods', FinancialPeriodController::class);
    Route::post('periods/{period}/activate', [FinancialPeriodController::class, 'activate'])->name('periods.activate');
    Route::resource('transactions', TransactionController::class)->except(['edit', 'update']);
    Route::get('transactions/{transaction}/receipt', [TransactionController::class, 'receipt'])->name('transactions.receipt');
    Route::get('transactions/{transaction}/pdf', [TransactionController::class, 'pdf'])->name('transactions.pdf');

    // ── Users (admin only) ─────────────────────────────────────
    Route::resource('users', UserController::class);

    // ── Reports ────────────────────────────────────────────────
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');

    // ── Settings ───────────────────────────────────────────────
    Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
});

// ── Admin JSON API ──────────────────────────────────────────────────────────
Route::prefix('admin/api')->middleware(['auth', 'admin'])->name('admin.api.')->group(function () {
    Route::get('dashboard-stats',              [\App\Http\Controllers\Admin\ApiController::class, 'dashboardStats'])->name('dashboard');
    Route::get('cases',                        [\App\Http\Controllers\Admin\ApiController::class, 'cases'])->name('cases.index');
    Route::get('cases/{case}',                 [\App\Http\Controllers\Admin\ApiController::class, 'caseDetail'])->name('cases.show');
    Route::post('cases',                       [\App\Http\Controllers\Admin\ApiController::class, 'quickCreateCase'])->name('cases.store');
    Route::patch('cases/{case}/status',        [\App\Http\Controllers\Admin\ApiController::class, 'updateCaseStatus'])->name('cases.status');
    Route::post('cases/{case}/close',          [\App\Http\Controllers\Admin\ApiController::class, 'closeCase'])->name('cases.close');
    Route::post('cases/{case}/reopen',         [\App\Http\Controllers\Admin\ApiController::class, 'reopenCase'])->name('cases.reopen');
    Route::post('cases/{case}/notes',          [\App\Http\Controllers\Admin\ApiController::class, 'addNote'])->name('cases.notes.store');
    Route::delete('notes/{note}',              [\App\Http\Controllers\Admin\ApiController::class, 'deleteNote'])->name('notes.destroy');
    Route::get('clients',                      [\App\Http\Controllers\Admin\ApiController::class, 'clients'])->name('clients.index');
    Route::get('clients/{client}',             [\App\Http\Controllers\Admin\ApiController::class, 'clientDetail'])->name('clients.show');
    Route::post('clients',                     [\App\Http\Controllers\Admin\ApiController::class, 'quickCreateClient'])->name('clients.store');
    Route::get('clients-select',               [\App\Http\Controllers\Admin\ApiController::class, 'clientsSelect'])->name('clients.select');
    Route::get('active-cases',                 [\App\Http\Controllers\Admin\ApiController::class, 'activeCases'])->name('cases.active');
    Route::post('transactions',                [\App\Http\Controllers\Admin\ApiController::class, 'quickCreateTransaction'])->name('transactions.store');
    Route::get('officers',                     [\App\Http\Controllers\Admin\ApiController::class, 'officers'])->name('officers');
    Route::get('accounts',                     [\App\Http\Controllers\Admin\ApiController::class, 'accounts'])->name('accounts');
    Route::get('active-period',                [\App\Http\Controllers\Admin\ApiController::class, 'activePeriod'])->name('period');
    // Self-profile
    Route::post('profile/update',              [\App\Http\Controllers\Admin\ApiController::class, 'profileUpdate'])->name('profile.update');
    Route::post('profile/avatar',              [\App\Http\Controllers\Admin\ApiController::class, 'profileAvatarUpdate'])->name('profile.avatar');
    Route::delete('profile/avatar',            [\App\Http\Controllers\Admin\ApiController::class, 'profileAvatarRemove'])->name('profile.avatar.remove');
    Route::post('profile/password',            [\App\Http\Controllers\Admin\ApiController::class, 'profilePasswordChange'])->name('profile.password');
    // Documents
    Route::post('documents/upload',            [\App\Http\Controllers\Admin\ApiController::class, 'uploadDocument'])->name('documents.upload');
    Route::delete('documents/{document}',      [\App\Http\Controllers\Admin\ApiController::class, 'deleteDocument'])->name('documents.delete');
    Route::get('cases/{case}/documents',       [\App\Http\Controllers\Admin\ApiController::class, 'caseDocuments'])->name('cases.documents');
    // Activity
    Route::get('activity',                     [\App\Http\Controllers\Admin\ApiController::class, 'recentActivity'])->name('activity');
});

require __DIR__ . '/auth.php';
