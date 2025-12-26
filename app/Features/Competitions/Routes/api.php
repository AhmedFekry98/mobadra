<?php

use App\Features\Competitions\Controllers\CompetitionController;
use App\Features\Competitions\Controllers\CompetitionParticipantController;
use App\Features\Competitions\Controllers\CompetitionTeamController;
use App\Features\Competitions\Controllers\CompetitionPhaseController;
use App\Features\Competitions\Controllers\CompetitionHackathonController;
use App\Features\Competitions\Controllers\CompetitionJudgeController;
use App\Features\Competitions\Controllers\EvaluationController;
use Illuminate\Support\Facades\Route;

// Competitions CRUD
Route::prefix('competitions')->name('competitions.')->group(function () {
    Route::get('', [CompetitionController::class, 'index'])->name('index');
    Route::post('', [CompetitionController::class, 'store'])->name('store');
    Route::get('{id}', [CompetitionController::class, 'show'])->name('show');
    Route::put('{id}', [CompetitionController::class, 'update'])->name('update');
    Route::patch('{id}', [CompetitionController::class, 'update']);
    Route::delete('{id}', [CompetitionController::class, 'destroy'])->name('destroy');

    // Phases
    Route::prefix('{competitionId}/phases')->name('phases.')->group(function () {
        Route::get('', [CompetitionPhaseController::class, 'index'])->name('index');
        Route::post('', [CompetitionPhaseController::class, 'store'])->name('store');
        Route::patch('{phaseId}', [CompetitionPhaseController::class, 'update'])->name('update');
        Route::delete('{phaseId}', [CompetitionPhaseController::class, 'destroy'])->name('destroy');
    });

    // Participants
    Route::prefix('{competitionId}/participants')->name('participants.')->group(function () {
        Route::get('', [CompetitionParticipantController::class, 'index'])->name('index');
        Route::get('{participantId}', [CompetitionParticipantController::class, 'show'])->name('show');
        Route::patch('{participantId}/status', [CompetitionParticipantController::class, 'updateStatus'])->name('update_status');
    });

    // Leaderboard
    Route::get('{competitionId}/leaderboard', [CompetitionParticipantController::class, 'leaderboard'])->name('leaderboard');

    // Teams
    Route::prefix('{competitionId}/teams')->name('teams.')->group(function () {
        Route::get('', [CompetitionTeamController::class, 'index'])->name('index');
        Route::post('', [CompetitionTeamController::class, 'store'])->name('store');
        Route::get('{teamId}', [CompetitionTeamController::class, 'show'])->name('show');
        Route::patch('{teamId}', [CompetitionTeamController::class, 'update'])->name('update');
        Route::delete('{teamId}', [CompetitionTeamController::class, 'destroy'])->name('destroy');
        Route::post('auto-form', [CompetitionTeamController::class, 'autoForm'])->name('auto_form');
    });

    // Hackathon
    Route::prefix('{competitionId}/hackathon')->name('hackathon.')->group(function () {
        Route::get('', [CompetitionHackathonController::class, 'index'])->name('index');
        Route::post('', [CompetitionHackathonController::class, 'store'])->name('store');
        Route::patch('{dayId}', [CompetitionHackathonController::class, 'update'])->name('update');
    });

    // Judges
    Route::prefix('{competitionId}/judges')->name('judges.')->group(function () {
        Route::get('', [CompetitionJudgeController::class, 'index'])->name('index');
        Route::post('', [CompetitionJudgeController::class, 'store'])->name('store');
        Route::delete('{judgeId}', [CompetitionJudgeController::class, 'destroy'])->name('destroy');
    });

    // Evaluations
    Route::prefix('{competitionId}/evaluations')->name('evaluations.')->group(function () {
        Route::post('phase2/{submissionId}', [EvaluationController::class, 'evaluatePhase2'])->name('phase2');
        Route::post('team/{teamId}', [EvaluationController::class, 'evaluateTeam'])->name('team');
        Route::get('team/{teamId}', [EvaluationController::class, 'getTeamEvaluations'])->name('team_list');
    });
});
