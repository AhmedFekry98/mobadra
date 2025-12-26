<?php

namespace App\Features\Competitions\Repositories;

use App\Features\Competitions\Models\CompetitionParticipant;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class CompetitionParticipantRepository
{
    public function getByCompetition(int $competitionId, bool $paginate = false, int $perPage = 15): Collection|LengthAwarePaginator
    {
        $query = CompetitionParticipant::where('competition_id', $competitionId)
            ->with('user')
            ->orderBy('rank');

        return $paginate ? $query->paginate($perPage) : $query->get();
    }

    public function find(int $id): ?CompetitionParticipant
    {
        return CompetitionParticipant::find($id);
    }

    public function findOrFail(int $id): CompetitionParticipant
    {
        return CompetitionParticipant::with('user')->findOrFail($id);
    }

    public function create(array $data): CompetitionParticipant
    {
        return CompetitionParticipant::create($data);
    }

    public function update(int $id, array $data): CompetitionParticipant
    {
        $participant = CompetitionParticipant::findOrFail($id);
        $participant->update($data);
        return $participant->fresh();
    }

    public function delete(int $id): bool
    {
        return CompetitionParticipant::destroy($id) > 0;
    }

    public function getByGovernorate(int $competitionId, string $governorate): Collection
    {
        return CompetitionParticipant::where('competition_id', $competitionId)
            ->where('governorate', $governorate)
            ->with('user')
            ->orderBy('rank')
            ->get();
    }

    public function getQualified(int $competitionId): Collection
    {
        return CompetitionParticipant::where('competition_id', $competitionId)
            ->where('status', 'qualified')
            ->with('user')
            ->orderBy('total_score', 'desc')
            ->get();
    }

    public function getLeaderboard(int $competitionId, ?string $governorate = null, int $limit = 100): Collection
    {
        $query = CompetitionParticipant::where('competition_id', $competitionId)
            ->with('user')
            ->orderBy('total_score', 'desc')
            ->limit($limit);

        if ($governorate) {
            $query->where('governorate', $governorate);
        }

        return $query->get();
    }

    public function updateRanks(int $competitionId): void
    {
        $participants = CompetitionParticipant::where('competition_id', $competitionId)
            ->orderBy('total_score', 'desc')
            ->get();

        $rank = 1;
        foreach ($participants as $participant) {
            $participant->update(['rank' => $rank++]);
        }
    }

    public function findByUserAndCompetition(int $userId, int $competitionId): ?CompetitionParticipant
    {
        return CompetitionParticipant::where('user_id', $userId)
            ->where('competition_id', $competitionId)
            ->first();
    }
}
