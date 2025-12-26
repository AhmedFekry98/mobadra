<?php

namespace App\Features\Competitions\Controllers;

use App\Features\Competitions\Models\CompetitionHackathonDay;
use App\Features\Competitions\Transformers\CompetitionHackathonDayResource;
use App\Traits\ApiResponses;
use App\Traits\HandleServiceExceptions;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class CompetitionHackathonController extends Controller
{
    use ApiResponses, HandleServiceExceptions, AuthorizesRequests;

    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function index(string $competitionId)
    {
        return $this->executeService(function () use ($competitionId) {
            $days = CompetitionHackathonDay::where('competition_id', $competitionId)
                ->orderBy('day_number')
                ->get();

            return $this->okResponse(
                CompetitionHackathonDayResource::collection($days),
                "Hackathon days retrieved successfully"
            );
        }, 'CompetitionHackathonController@index');
    }

    public function store(Request $request, string $competitionId)
    {
        return $this->executeService(function () use ($request, $competitionId) {
            $request->validate([
                'day_number' => ['required', 'integer', 'min:1'],
                'title' => ['required', 'string', 'max:255'],
                'title_ar' => ['required', 'string', 'max:255'],
                'description' => ['nullable', 'string'],
                'date' => ['required', 'date'],
                'level' => ['required', 'in:governorate,national,final'],
            ]);

            $day = CompetitionHackathonDay::create([
                'competition_id' => $competitionId,
                ...$request->only(['day_number', 'title', 'title_ar', 'description', 'date', 'level']),
            ]);

            return $this->createdResponse(
                CompetitionHackathonDayResource::make($day),
                "Hackathon day created successfully"
            );
        }, 'CompetitionHackathonController@store');
    }

    public function update(Request $request, string $competitionId, string $dayId)
    {
        return $this->executeService(function () use ($request, $dayId) {
            $request->validate([
                'status' => ['sometimes', 'in:upcoming,active,completed'],
                'teams_count' => ['sometimes', 'integer', 'min:0'],
            ]);

            $day = CompetitionHackathonDay::findOrFail($dayId);
            $day->update($request->only(['status', 'teams_count']));

            return $this->okResponse(
                CompetitionHackathonDayResource::make($day->fresh()),
                "Hackathon day updated successfully"
            );
        }, 'CompetitionHackathonController@update');
    }
}
