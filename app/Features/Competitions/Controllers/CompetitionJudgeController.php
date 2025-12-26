<?php

namespace App\Features\Competitions\Controllers;

use App\Features\Competitions\Models\CompetitionJudge;
use App\Traits\ApiResponses;
use App\Traits\HandleServiceExceptions;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class CompetitionJudgeController extends Controller
{
    use ApiResponses, HandleServiceExceptions, AuthorizesRequests;

    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function index(string $competitionId)
    {
        return $this->executeService(function () use ($competitionId) {
            $judges = CompetitionJudge::where('competition_id', $competitionId)->get();

            return $this->okResponse($judges, "Judges retrieved successfully");
        }, 'CompetitionJudgeController@index');
    }

    public function store(Request $request, string $competitionId)
    {
        return $this->executeService(function () use ($request, $competitionId) {
            $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'max:255'],
                'specialty' => ['required', 'string', 'max:255'],
                'avatar' => ['nullable', 'string', 'max:500'],
            ]);

            $judge = CompetitionJudge::create([
                'competition_id' => $competitionId,
                ...$request->only(['name', 'email', 'specialty', 'avatar']),
            ]);

            return $this->createdResponse($judge, "Judge added successfully");
        }, 'CompetitionJudgeController@store');
    }

    public function destroy(string $competitionId, string $judgeId)
    {
        return $this->executeService(function () use ($judgeId) {
            CompetitionJudge::destroy($judgeId);

            return $this->okResponse(null, "Judge removed successfully");
        }, 'CompetitionJudgeController@destroy');
    }
}
