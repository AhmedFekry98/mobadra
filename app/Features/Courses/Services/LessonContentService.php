<?php

namespace App\Features\Courses\Services;

use App\Features\Courses\Metadata\LessonContentMetadata;
use App\Features\Courses\Models\LessonContent;
use App\Features\Courses\Models\VideoContent;
use App\Features\Courses\Models\LiveSession;
use App\Features\Courses\Models\Quiz;
use App\Features\Courses\Models\Assignment;
use App\Features\Courses\Models\Material;
use App\Features\Courses\Repositories\LessonContentRepository;
use App\Traits\HasGlobalQueryHandlers;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class LessonContentService
{
    use HasGlobalQueryHandlers;

    public function __construct(
        protected LessonContentRepository $repository
    ) {}

    public function getLessonContents(?string $search = null, ?array $filter = null, ?array $sort = null, ?bool $paginate = false): Collection|LengthAwarePaginator
    {
        $query = $this->repository->query()->with(['lesson', 'contentable']);

        $data = [
            'search' => $search,
            'filter' => $filter,
            'sort' => $sort,
        ];

        $config = [
            'searchable_columns' => LessonContentMetadata::getSearchableColumns(),
            'filters' => LessonContentMetadata::getFilters(),
            'operators' => LessonContentMetadata::getOperators(),
            'sortable_columns' => $this->getSortableColumns(),
        ];

        return $this->getWithGlobalHandlers($query, $data, $config, $paginate);
    }

    protected function getSortableColumns(): array
    {
        return collect(LessonContentMetadata::getFilters())
            ->pluck('column')
            ->toArray();
    }

    public function storeLessonContent(array $data): LessonContent
    {
        return DB::transaction(function () use ($data) {
            $contentType = $data['content_type'];
            $contentData = $data['content_data'] ?? [];

            // Create the polymorphic content first
            $contentable = $this->createContentable($contentType, $contentData);

            // Create the lesson content with the polymorphic relation
            $lessonContentData = collect($data)->except(['content_data'])->toArray();
            $lessonContentData['contentable_type'] = get_class($contentable);
            $lessonContentData['contentable_id'] = $contentable->id;

            return $this->repository->create($lessonContentData);
        });
    }

    protected function createContentable(string $contentType, array $data)
    {
        return match ($contentType) {
            'video' => VideoContent::create([
                'video_url' => $data['video_url'],
                'video_provider' => $data['video_provider'] ?? 'youtube',
                'duration' => $data['duration'] ?? 0,
                'thumbnail_url' => $data['thumbnail_url'] ?? null,
            ]),
            'live_session' => LiveSession::create([
                'meeting_url' => $data['meeting_url'] ?? null,
                'meeting_provider' => $data['meeting_provider'] ?? 'zoom',
                'start_time' => $data['start_time'],
                'end_time' => $data['end_time'] ?? null,
                'max_participants' => $data['max_participants'] ?? null,
            ]),
            'quiz' => Quiz::create([
                'time_limit' => $data['time_limit'] ?? null,
                'passing_score' => $data['passing_score'] ?? 60,
                'max_attempts' => $data['max_attempts'] ?? 1,
                'shuffle_questions' => $data['shuffle_questions'] ?? false,
                'show_answers' => $data['show_answers'] ?? false,
            ]),
            'assignment' => Assignment::create([
                'instructions' => $data['instructions'] ?? null,
                'due_date' => $data['due_date'] ?? null,
                'max_score' => $data['max_score'] ?? 100,
                'allow_late_submission' => $data['allow_late_submission'] ?? false,
                'allowed_file_types' => $data['allowed_file_types'] ?? null,
                'max_file_size' => $data['max_file_size'] ?? null,
            ]),
            'material' => Material::create([
                'file_url' => $data['file_url'],
                'file_type' => $data['file_type'],
                'file_size' => $data['file_size'] ?? null,
                'is_downloadable' => $data['is_downloadable'] ?? true,
            ]),
            default => throw new \InvalidArgumentException("Invalid content type: {$contentType}"),
        };
    }

    public function getLessonContentById(string $id): ?LessonContent
    {
        return $this->repository->findOrFail($id);
    }

    public function updateLessonContent(string $id, array $data): LessonContent
    {
        return DB::transaction(function () use ($id, $data) {
            $lessonContent = $this->repository->findOrFail($id);
            $lessonContent->load('contentable');

            // Update the polymorphic content if content_data is provided
            $contentData = $data['content_data'] ?? $data['contentData'] ?? null;
            if ($contentData && $lessonContent->contentable) {
                $lessonContent->contentable->update($contentData);
            }

            // Update the lesson content
            $lessonContentData = collect($data)->except(['content_data', 'contentData'])->toArray();
            $updated = $this->repository->update($id, $lessonContentData);
            $updated->load('contentable');

            return $updated;
        });
    }

    public function deleteLessonContent(string $id): bool
    {
        return DB::transaction(function () use ($id) {
            $lessonContent = $this->repository->findOrFail($id);

            // Delete the polymorphic content first
            if ($lessonContent->contentable) {
                $lessonContent->contentable->delete();
            }

            return $this->repository->delete($id);
        });
    }

    public function getLessonContentsByLessonId(string $lessonId): Collection
    {
        return $this->repository->getByLessonId($lessonId);
    }

    public function lessonContentExists(string $id): bool
    {
        return $this->repository->exists($id);
    }

    public function getTotalCount(): int
    {
        return $this->repository->count();
    }
}
