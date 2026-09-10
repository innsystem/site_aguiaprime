<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\BonificacaoController;
use App\Http\Controllers\Admin\CotacaoController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ModalidadeController;
use App\Http\Controllers\Admin\ResultadoController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('login', [AuthController::class, 'showLogin'])->name('login');
        Route::post('login', [AuthController::class, 'login'])->name('login.submit');
    });

    Route::middleware('auth')->group(function () {
        Route::post('logout', [AuthController::class, 'logout'])->name('logout');
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        Route::get('settings', [SettingController::class, 'edit'])->name('settings.edit');
        Route::put('settings', [SettingController::class, 'update'])->name('settings.update');

        Route::get('cotacoes', [CotacaoController::class, 'index'])->name('cotacoes.index');
        Route::post('cotacoes', [CotacaoController::class, 'store'])->name('cotacoes.store');
        Route::put('cotacoes/{cotacao}', [CotacaoController::class, 'update'])->name('cotacoes.update');
        Route::delete('cotacoes/{cotacao}', [CotacaoController::class, 'destroy'])->name('cotacoes.destroy');

        Route::get('bonificacoes', [BonificacaoController::class, 'index'])->name('bonificacoes.index');
        Route::put('bonificacoes', [BonificacaoController::class, 'update'])->name('bonificacoes.update');

        Route::get('modalidades', [ModalidadeController::class, 'index'])->name('modalidades.index');
        Route::put('modalidades', [ModalidadeController::class, 'update'])->name('modalidades.update');

        Route::resource('resultados', ResultadoController::class)->except(['show']);
        Route::resource('users', UserController::class)->except(['show']);
    });
});

Route::redirect('/login', '/admin/login')->name('login');
