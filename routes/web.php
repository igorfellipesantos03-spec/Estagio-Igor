<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HackathonController;
use App\Http\Controllers\GrupoController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\RankingController;
use App\Http\Controllers\NotificationController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Rotas de Perfil (compartilhadas)
Route::middleware(['auth'])->group(function () {
    Route::get('/perfil', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/perfil', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
});

// Rotas de Notificações (compartilhadas - qualquer usuário autenticado)
Route::middleware(['auth'])->prefix('api/notifications')->group(function () {
    Route::get('/', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/mark-read', [NotificationController::class, 'markAsRead'])->name('notifications.markRead');
    Route::post('/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllRead');
    Route::get('/unread-count', [NotificationController::class, 'unreadCount'])->name('notifications.unreadCount');
});

Route::middleware(['auth']) 
    ->prefix('dashboard/aluno')
    ->group(function () {
        

        Route::get('/', [DashboardController::class, 'aluno'])->name('dashboard.aluno');

        Route::get('/hackathon', [HackathonController::class, 'alunoIndex'])->name('aluno.hackathons.index');
        
        // Rotas de Grupos (Aluno)
        Route::get('/grupos', [GrupoController::class, 'index'])->name('grupos.index');
        Route::post('/grupos', [GrupoController::class, 'store'])->name('grupos.store');
        Route::post('/grupos/join', [GrupoController::class, 'join'])->name('grupos.join');
        Route::put('/grupos/{grupo}', [GrupoController::class, 'update'])->name('grupos.update');
        Route::delete('/grupos/{grupo}', [GrupoController::class, 'destroy'])->name('grupos.destroy');
        Route::delete('/grupos/{grupo}/membros/{user}', [GrupoController::class, 'removeMember'])->name('grupos.removeMember');
        Route::post('/grupos/{grupo}/leave', [GrupoController::class, 'leave'])->name('grupos.leave');

        // Rotas de Presença (Aluno)
        Route::get('/presenca', [AttendanceController::class, 'create'])->name('aluno.presenca.create');
        Route::post('/presenca', [AttendanceController::class, 'store'])->name('aluno.presenca.store');

        // Ranking
        Route::get('/ranking', [RankingController::class, 'index'])->name('aluno.ranking');
    });

Route::middleware(['auth', 'role:professor'])
    ->prefix('dashboard/professor')
    ->group(function () {

        Route::get('/', [DashboardController::class, 'professor'])->name('dashboard.professor');
        

        Route::get('/hackathons', [HackathonController::class, 'index'])->name('hackathons.index');
        Route::post('/hackathons', [HackathonController::class, 'store'])->name('hackathons.store');
        Route::put('/hackathons/{hackathon}', [HackathonController::class, 'update'])->name('hackathons.update');
        Route::post('/hackathons/{hackathon}/finalize', [HackathonController::class, 'finalize'])->name('hackathons.finalize');
        Route::delete('/hackathons/{hackathon}', [HackathonController::class, 'destroy'])->name('hackathons.destroy');

        // Rotas de Validação de Presença (Professor)
        Route::get('/presencas', [AttendanceController::class, 'hackathonList'])->name('professor.presenca.hackathons');
        Route::get('/presencas/{hackathon}', [AttendanceController::class, 'index'])->name('professor.presenca.index');
        Route::patch('/presencas/{attendance}', [AttendanceController::class, 'update'])->name('professor.presenca.update');
        Route::get('/presencas/{attendance}/foto', [AttendanceController::class, 'showPhoto'])->name('professor.presenca.foto');

        // Rotas de Grupos (Professor)
        Route::get('/grupos', [GrupoController::class, 'professorIndex'])->name('professor.grupos.index');
        Route::delete('/grupos/{grupo}', [GrupoController::class, 'professorDestroy'])->name('professor.grupos.destroy');
        Route::delete('/grupos/{grupo}/imagem', [GrupoController::class, 'professorRemoveImage'])->name('professor.grupos.removeImage');

        // Rotas de Relatórios (Professor)
        Route::get('/relatorios', [\App\Http\Controllers\ReportController::class, 'index'])->name('reports.index');
        Route::get('/relatorios/exportar', [\App\Http\Controllers\ReportController::class, 'export'])->name('reports.export');
    });


Route::middleware(['auth', 'role:adm'])->group(function () {
    Route::resource('users', UserController::class);
});