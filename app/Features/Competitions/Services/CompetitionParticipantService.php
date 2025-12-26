<?php

namespace App\Features\Competitions\Services;

use App\Features\Competitions\Models\CompetitionParticipant;
use App\Features\Competitions\Repositories\CompetitionParticipantRepository;
use App\Features\Competitions\Repositories\CompetitionRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class CompetitionParticipantService
{
    public function __construct(
        protected CompetitionParticipantRepository $repository,
        protected CompetitionRepository $competitionRepository
    ) {}

    public function getParticipantsByCompetition(int $competitionId, bool $paginate = false, int $perPage = 15): Collection|LengthAwarePaginator
    {
        return $this->repository->getByCompetition($competitionId, $paginate, $perPage);
    }

    public function getParticipantById(int $id): CompetitionParticipant
    {
        return $this->repository->findOrFail($id);
    }

    public function registerParticipant(int $competitionId, int $userId, string $governorate): CompetitionParticipant
    {
        $participant = $this->repository->create([
            'competition_id' => $competitionId,
            'user_id' => $userId,
            'governorate' => $governorate,
            'status' => 'registered',
        ]);

        // Update competition counts
        $competition = $this->competitionRepository->findOrFail($competitionId);
        $competition->updateCounts();

        return $participant;
    }

    public function updateParticipantStatus(int $id, string $status): CompetitionParticipant
    {
        $participant = $this->repository->update($id, ['status' => $status]);

        // Update competition counts
        $competition = $this->competitionRepository->findOrFail($participant->competition_id);
        $competition->updateCounts();

        return $participant;
    }

    public function updateParticipantScore(int $id, string $phase, float $score): CompetitionParticipant
    {
        $scoreField = "phase{$phase}_score";
        $participant = $this->repository->update($id, [$scoreField => $score]);

        // Recalculate total score
        $participant->calculateTotalScore();

        // Update ranks
        $this->repository->updateRanks($participant->competition_id);

        return $participant->fresh();
    }

    public function getLeaderboard(int $competitionId, ?string $governorate = null, int $limit = 100): Collection
    {
        return $this->repository->getLeaderboard($competitionId, $governorate, $limit);
    }

    public function getQualifiedParticipants(int $competitionId): Collection
    {
        return $this->repository->getQualified($competitionId);
    }

    public function getByGovernorate(int $competitionId, string $governorate): Collection
    {
        return $this->repository->getByGovernorate($competitionId, $governorate);
    }
}
