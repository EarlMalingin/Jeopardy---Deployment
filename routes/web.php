<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\JeopardyController;
use App\Http\Controllers\SimpleCustomController;

Route::get('/', function () {
    return redirect('/jeopardy');
});

// Jeopardy Game Routes
Route::get('/jeopardy', [JeopardyController::class, 'index'])->name('jeopardy.index');
Route::get('/jeopardy/setup', [JeopardyController::class, 'setup'])->name('jeopardy.setup');
Route::get('/jeopardy/play', [JeopardyController::class, 'play'])->name('jeopardy.play');
Route::get('/jeopardy/game-state', [JeopardyController::class, 'getGameState'])->name('jeopardy.game-state');
Route::post('/jeopardy/start', [JeopardyController::class, 'startGame'])->name('jeopardy.start');
Route::post('/jeopardy/question', [JeopardyController::class, 'selectQuestion'])->name('jeopardy.question');
Route::post('/jeopardy/answer', [JeopardyController::class, 'submitAnswer'])->name('jeopardy.answer');
Route::post('/jeopardy/timer', [JeopardyController::class, 'updateTimer'])->name('jeopardy.timer');
Route::post('/jeopardy/reset', [JeopardyController::class, 'resetGame'])->name('jeopardy.reset');
Route::get('/jeopardy/categories', [JeopardyController::class, 'getCategories'])->name('jeopardy.categories');
Route::get('/jeopardy/test-deduction', [JeopardyController::class, 'testDeduction'])->name('jeopardy.test-deduction');
Route::get('/jeopardy/debug-state', [JeopardyController::class, 'debugGameState'])->name('jeopardy.debug-state');
Route::post('/jeopardy/start-custom', [JeopardyController::class, 'startCustomGame'])->name('jeopardy.start-custom');
Route::post('/jeopardy/simple-start-custom', [SimpleCustomController::class, 'startGame'])->name('jeopardy.simple-start-custom');
Route::get('/jeopardy/simple-game-state', [SimpleCustomController::class, 'getGameState'])->name('jeopardy.simple-game-state');
Route::post('/jeopardy/simple-question', [SimpleCustomController::class, 'selectQuestion'])->name('jeopardy.simple-question');
Route::post('/jeopardy/simple-answer', [SimpleCustomController::class, 'submitAnswer'])->name('jeopardy.simple-answer');
Route::post('/jeopardy/simple-timer', [SimpleCustomController::class, 'updateTimer'])->name('jeopardy.simple-timer');
Route::post('/jeopardy/simple-reset', [SimpleCustomController::class, 'resetGame'])->name('jeopardy.simple-reset');
Route::get('/jeopardy/test-turn-advancement', [SimpleCustomController::class, 'testTurnAdvancement'])->name('jeopardy.test-turn-advancement');
Route::get('/jeopardy/test-turn-sequence', [SimpleCustomController::class, 'testTurnSequence'])->name('jeopardy.test-turn-sequence');
Route::get('/jeopardy/fix-game-state', [SimpleCustomController::class, 'fixGameState'])->name('jeopardy.fix-game-state');
Route::get('/jeopardy/custom-game', [JeopardyController::class, 'customGameCreator'])->name('jeopardy.custom-game');
Route::get('/jeopardy/simple-custom-game', [SimpleCustomController::class, 'index'])->name('jeopardy.simple-custom-game');
Route::get('/jeopardy/play-custom', [JeopardyController::class, 'playCustomGame'])->name('jeopardy.play-custom');
Route::get('/jeopardy/simple-play-custom', [SimpleCustomController::class, 'play'])->name('jeopardy.simple-play-custom');
Route::get('/jeopardy/debug-session', [JeopardyController::class, 'debugSession'])->name('jeopardy.debug-session');
Route::get('/jeopardy/lobby', [JeopardyController::class, 'lobbySelection'])->name('jeopardy.lobby');
Route::post('/jeopardy/create-lobby', [JeopardyController::class, 'createLobby'])->name('jeopardy.create-lobby');
Route::post('/jeopardy/join-lobby', [JeopardyController::class, 'joinLobby'])->name('jeopardy.join-lobby');
Route::get('/jeopardy/lobby/{code}', [JeopardyController::class, 'lobbyRoom'])->name('jeopardy.lobby-room');
Route::post('/jeopardy/lobby/{code}/start', [JeopardyController::class, 'startLobbyGame'])->name('jeopardy.start-lobby-game');
Route::post('/jeopardy/start-custom-game-from-lobby', [JeopardyController::class, 'startCustomGameFromLobby'])->name('jeopardy.start-custom-game-from-lobby');
Route::post('/jeopardy/get-lobby-game-state', [JeopardyController::class, 'getLobbyGameState'])->name('jeopardy.get-lobby-game-state');
Route::post('/jeopardy/update-lobby-game-state', [JeopardyController::class, 'updateLobbyGameState'])->name('jeopardy.update-lobby-game-state');
Route::post('/jeopardy/reset-lobby-game', [JeopardyController::class, 'resetLobbyGame'])->name('jeopardy.reset-lobby-game');
Route::get('/jeopardy/debug-player', [JeopardyController::class, 'debugPlayer'])->name('jeopardy.debug-player');
Route::post('/jeopardy/switch-player', [JeopardyController::class, 'switchPlayer'])->name('jeopardy.switch-player');
Route::post('/jeopardy/auto-assign-player', [JeopardyController::class, 'autoAssignPlayer'])->name('jeopardy.auto-assign-player');
Route::get('/jeopardy/lobby/{code}/status', [JeopardyController::class, 'getLobbyStatus'])->name('jeopardy.lobby-status');
Route::post('/jeopardy/clear-lobby-info', [JeopardyController::class, 'clearLobbyInfo'])->name('jeopardy.clear-lobby-info');
Route::get('/jeopardy/debug-team-assignment', [JeopardyController::class, 'debugTeamAssignment'])->name('jeopardy.debug-team-assignment');
