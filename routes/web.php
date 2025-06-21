<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\CustomFieldController;
Route::get('/', function () {
    return redirect('/login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/contacts', [ContactController::class, 'index'])->name('contacts');
    Route::get('/contacts/filter', [ContactController::class, 'filter'])->name('contacts.filter');
    Route::post('/contacts', [ContactController::class, 'store'])->name('contacts.store');
    Route::put('/contacts/{id}', [ContactController::class, 'update'])->name('contacts.update');
    Route::delete('/contacts/{id}', [ContactController::class, 'destroy'])->name('contacts.destroy');
    Route::resource('custom-fields', CustomFieldController::class);
    Route::post('/contacts/merge', [ContactController::class, 'mergeSubmit'])->name('contacts.merge.submit');
    Route::get('/contacts/merge-history', [ContactController::class, 'mergeHistory'])->name('contacts.mergeHistory');
});

require __DIR__.'/auth.php';
