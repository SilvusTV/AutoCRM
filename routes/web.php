<?php

use App\Http\Controllers\BankAccountController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\InvoiceLineController;
use App\Http\Controllers\PaymentMethodController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TimeEntryController;
use App\Http\Controllers\UrssafController;
use App\Http\Controllers\UrssafDeclarationController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Client routes
    Route::resource('clients', ClientController::class);

    // Company routes
    Route::resource('companies', CompanyController::class);

    // Project routes
    Route::resource('projects', ProjectController::class);

    // Time Entry routes
    Route::resource('time-entries', TimeEntryController::class);
    Route::get('time-entries-export', [TimeEntryController::class, 'export'])->name('time-entries.export');
    Route::get('time-entries-calendar', [TimeEntryController::class, 'calendar'])->name('time-entries.calendar');

    // Invoice routes
    Route::resource('invoices', InvoiceController::class);
    Route::get('invoices/{id}/pdf', [InvoiceController::class, 'generatePdf'])->name('invoices.pdf');
    Route::get('invoices/{id}/preview', [InvoiceController::class, 'preview'])->name('invoices.preview');
    Route::post('invoices/{id}/validate', [InvoiceController::class, 'validateInvoice'])->name('invoices.validate');
    Route::patch('invoices/{id}/status', [InvoiceController::class, 'updateStatus'])->name('invoices.update-status');
    Route::get('quotes/create', [InvoiceController::class, 'createQuote'])->name('quotes.create');

    // Invoice Line routes
    Route::resource('invoice-lines', InvoiceLineController::class);

    // URSSAF Declaration routes
    Route::resource('urssaf-declarations', UrssafDeclarationController::class);
    Route::post('urssaf-declarations/calculate-revenue', [UrssafDeclarationController::class, 'calculateRevenue'])->name('urssaf-declarations.calculate-revenue');

    // URSSAF Profile routes
    Route::patch('/urssaf', [UrssafController::class, 'update'])->name('urssaf.update');

    // Bank Account routes
    Route::post('bank-accounts', [BankAccountController::class, 'store'])->name('bank-accounts.store');
    Route::get('bank-accounts/{bankAccount}/edit', [BankAccountController::class, 'edit'])->name('bank-accounts.edit');
    Route::patch('bank-accounts/{bankAccount}', [BankAccountController::class, 'update'])->name('bank-accounts.update');
    Route::patch('bank-accounts/{bankAccount}/set-default', [BankAccountController::class, 'setDefault'])->name('bank-accounts.set-default');
    Route::delete('bank-accounts/{bankAccount}', [BankAccountController::class, 'destroy'])->name('bank-accounts.destroy');

    // Payment Method routes
    Route::get('payment-methods/create', [PaymentMethodController::class, 'create'])->name('payment-methods.create');
    Route::post('payment-methods', [PaymentMethodController::class, 'store'])->name('payment-methods.store');
    Route::delete('payment-methods/{paymentMethod}', [PaymentMethodController::class, 'destroy'])->name('payment-methods.destroy');
});

require __DIR__.'/auth.php';
