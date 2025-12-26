<?php

namespace App\Features\Competitions\Repositories;

use App\Features\Competitions\Models\CompetitionTeam;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class CompetitionTeamRepository
{
    public function getByCompetition(int $competitionId, bool $paginate = false, int $perPage = 15): Collection|LengthAwarePaginator
    {
        $query = CompetitionTeam::where('competition_id', $competitionId)
            ->with(['members.participant.user'])
            ->orderBy('rank');

        return $paginate ? $query->paginate($perPage) : $query->get();
    }

    public function find(int $id): ?CompetitionTeam
    {
        return CompetitionTeam::find($id);
    }

    public function findOrFail(int $id): CompetitionTeam
    {
        return CompetitionTeam::with(['members.participant.user'])->findOrFail($id);
    }

    public function create(array $data): CompetitionTeam
    {
        return CompetitionTeam::create($data);
    }

    public function update(int $id, array $data): CompetitionTeam
    {
        $team = CompetitionTeam::findOrFail($id);
        $team->update($data);
        return $team->fresh();
    }

    public function delete(int $id): bool
    {
        return CompetitionTeam::destroy($id) > 0;
    }

    public function getByGovernorate(int $competitionId, string $governorate): Collection
    {
        return CompetitionTeam::where('competition_id', $competitionId)
            ->where('governorate', $governorate)
            ->with(['members.participant.user'])
            ->orderBy('rank')
            ->get();
    }

    public function updateRanks(int $competitionId): void
    {
        $teams = CompetitionTeam::where('competition_id', $competitionId)
            ->orderBy('total_score', 'desc')
            ->get();

        $rank = 1;
        foreach ($teams as $team) {
            $team->update(['rank' => $rank++]);
        }
    }
}
