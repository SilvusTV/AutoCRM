<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\InvoiceLineController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TimeEntryController;
use App\Http\Controllers\UrssafDeclarationController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Client routes
    Route::resource('clients', ClientController::class);

    // Project routes
    Route::resource('projects', ProjectController::class);

    // Time Entry routes
    Route::resource('time-entries', TimeEntryController::class);
    Route::get('time-entries-export', [TimeEntryController::class, 'export'])->name('time-entries.export');
    Route::get('time-entries-calendar', [TimeEntryController::class, 'calendar'])->name('time-entries.calendar');

    // Invoice routes
    Route::resource('invoices', InvoiceController::class);
    Route::get('invoices/{id}/pdf', [InvoiceController::class, 'generatePdf'])->name('invoices.pdf');

    // Invoice Line routes
    Route::resource('invoice-lines', InvoiceLineController::class);

    // URSSAF Declaration routes
    Route::resource('urssaf-declarations', UrssafDeclarationController::class);
});

require __DIR__.'/auth.php';
