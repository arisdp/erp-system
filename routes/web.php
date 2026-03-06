<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Master\CompanyController;




Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::prefix('master')->group(function () {
        Route::resource('companies', \App\Http\Controllers\Master\CompanyController::class);
        Route::resource('branches', \App\Http\Controllers\Master\BranchController::class);
        Route::resource('departments', \App\Http\Controllers\Master\DepartmentController::class);
        Route::resource('users', \App\Http\Controllers\Master\UserController::class);
        Route::resource('roles', \App\Http\Controllers\Master\RoleController::class);
        Route::resource('accounts', \App\Http\Controllers\Master\AccountController::class);
    });
});

require __DIR__ . '/auth.php';
