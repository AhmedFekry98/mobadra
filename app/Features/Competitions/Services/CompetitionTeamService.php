<?php

namespace App\Features\Competitions\Services;

use App\Features\Competitions\Models\CompetitionTeam;
use App\Features\Competitions\Models\CompetitionTeamMember;
use App\Features\Competitions\Repositories\CompetitionParticipantRepository;
use App\Features\Competitions\Repositories\CompetitionRepository;
use App\Features\Competitions\Repositories\CompetitionTeamRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class CompetitionTeamService
{
    public function __construct(
        protected CompetitionTeamRepository $repository,
        protected CompetitionParticipantRepository $participantRepository,
        protected CompetitionRepository $competitionRepository
    ) {}

    public function getTeamsByCompetition(int $competitionId, bool $paginate = false, int $perPage = 15): Collection|LengthAwarePaginator
    {
        return $this->repository->getByCompetition($competitionId, $paginate, $perPage);
    }

    public function getTeamById(int $id): CompetitionTeam
    {
        return $this->repository->findOrFail($id);
    }

    public function createTeam(int $competitionId, array $data, array $memberIds = []): CompetitionTeam
    {
        $team = $this->repository->create([
            'competition_id' => $competitionId,
            'name' => $data['name'],
            'track' => $data['track'],
            'lab' => $data['lab'] ?? null,
            'governorate' => $data['governorate'],
        ]);

        // Add members
        foreach ($memberIds as $participantId) {
            $participant = $this->participantRepository->findOrFail($participantId);

            CompetitionTeamMember::create([
                'team_id' => $team->id,
                'participant_id' => $participantId,
                'role' => 'Research', // Default role
                'tier' => $participant->getTier(),
            ]);

            // Update participant's team_id
            $this->participantRepository->update($participantId, ['team_id' => $team->id]);
        }

        // Update competition counts
        $competition = $this->competitionRepository->findOrFail($competitionId);
        $competition->updateCounts();

        return $team->load('members.participant.user');
    }

    public function updateTeam(int $id, array $data): CompetitionTeam
    {
        return $this->repository->update($id, $data);
    }

    public function deleteTeam(int $id): bool
    {
        $team = $this->repository->findOrFail($id);
        $competitionId = $team->competition_id;

        // Remove team_id from participants
        foreach ($team->members as $member) {
            $this->participantRepository->update($member->participant_id, ['team_id' => null]);
        }

        $result = $this->repository->delete($id);

        // Update competition counts
        $competition = $this->competitionRepository->findOrFail($competitionId);
        $competition->updateCounts();

        return $result;
    }

    public function addMember(int $teamId, int $participantId, string $role): CompetitionTeamMember
    {
        $team = $this->repository->findOrFail($teamId);
        $participant = $this->participantRepository->findOrFail($participantId);

        $member = CompetitionTeamMember::create([
            'team_id' => $teamId,
            'participant_id' => $participantId,
            'role' => $role,
            'tier' => $participant->getTier(),
        ]);

        $this->participantRepository->update($participantId, ['team_id' => $teamId]);

        return $member;
    }

    public function removeMember(int $teamId, int $participantId): bool
    {
        $member = CompetitionTeamMember::where('team_id', $teamId)
            ->where('participant_id', $participantId)
            ->first();

        if ($member) {
            $this->participantRepository->update($participantId, ['team_id' => null]);
            return $member->delete();
        }

        return false;
    }

    public function updateMemberRole(int $teamId, int $participantId, string $role): CompetitionTeamMember
    {
        $member = CompetitionTeamMember::where('team_id', $teamId)
            ->where('participant_id', $participantId)
            ->firstOrFail();

        $member->update(['role' => $role]);
        return $member->fresh();
    }

    /**
     * Auto-form teams based on tier distribution
     * 2 High + 2 Mid + 1 Emerging per team
     */
    public function autoFormTeams(int $competitionId, string $governorate): array
    {
        $qualified = $this->participantRepository->getQualified($competitionId);
        $byGovernorate = $qualified->where('governorate', $governorate)->whereNull('team_id');

        // Classify by tier
        $high = $byGovernorate->filter(fn($p) => $p->getTier() === 'High')->values();
        $mid = $byGovernorate->filter(fn($p) => $p->getTier() === 'Mid')->values();
        $emerging = $byGovernorate->filter(fn($p) => $p->getTier() === 'Emerging')->values();

        $teams = [];
        $teamNumber = 1;

        while ($high->count() >= 2 && $mid->count() >= 2 && $emerging->count() >= 1) {
            $members = [
                $high->shift()->id,
                $high->shift()->id,
                $mid->shift()->id,
                $mid->shift()->id,
                $emerging->shift()->id,
            ];

            $team = $this->createTeam($competitionId, [
                'name' => "Team {$governorate} #{$teamNumber}",
                'track' => 'online',
                'governorate' => $governorate,
            ], $members);

            $teams[] = $team;
            $teamNumber++;
        }

        return $teams;
    }
}
