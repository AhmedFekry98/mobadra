<?php

namespace App\Features\Competitions\Services;

use App\Features\Competitions\Models\Phase2Submission;
use App\Features\Competitions\Models\TeamEvaluation;
use App\Features\Competitions\Repositories\CompetitionParticipantRepository;
use App\Features\Competitions\Repositories\CompetitionTeamRepository;

class EvaluationService
{
    public function __construct(
        protected CompetitionParticipantRepository $participantRepository,
        protected CompetitionTeamRepository $teamRepository
    ) {}

    public function submitPhase2Evaluation(int $submissionId, array $data, int $evaluatorId): Phase2Submission
    {
        $submission = Phase2Submission::findOrFail($submissionId);

        $submission->update([
            'idea_clarity' => $data['idea_clarity'],
            'technical_understanding' => $data['technical_understanding'],
            'logic_analysis' => $data['logic_analysis'],
            'presentation_communication' => $data['presentation_communication'],
            'feedback' => $data['feedback'] ?? null,
            'evaluated_by' => $evaluatorId,
            'evaluated_at' => now(),
        ]);

        $submission->calculateTotalScore();

        return $submission->fresh();
    }

    public function submitTeamEvaluation(int $teamId, array $data, int $evaluatorId): TeamEvaluation
    {
        $evaluation = TeamEvaluation::create([
            'team_id' => $teamId,
            'evaluator_id' => $evaluatorId,
            'idea_strength' => $data['idea_strength'],
            'implementation' => $data['implementation'],
            'teamwork' => $data['teamwork'],
            'problem_solving' => $data['problem_solving'],
            'final_presentation' => $data['final_presentation'],
            'feedback' => $data['feedback'] ?? null,
        ]);

        $evaluation->calculateTotalScore();

        // Update team ranks
        $team = $this->teamRepository->findOrFail($teamId);
        $this->teamRepository->updateRanks($team->competition_id);

        return $evaluation->fresh();
    }

    public function getPhase2Submission(int $participantId): ?Phase2Submission
    {
        return Phase2Submission::where('participant_id', $participantId)->first();
    }

    public function submitPhase2Video(int $participantId, string $videoUrl): Phase2Submission
    {
        return Phase2Submission::create([
            'participant_id' => $participantId,
            'video_url' => $videoUrl,
            'submitted_at' => now(),
        ]);
    }

    public function getTeamEvaluations(int $teamId): \Illuminate\Database\Eloquent\Collection
    {
        return TeamEvaluation::where('team_id', $teamId)->with('evaluator')->get();
    }
}
