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
            'quiz' => Quiz::create([
                'time_limit' => $data['time_limit'] ?? null,
                'passing_score' => $data['passing_score'] ?? 60,
                'max_attempts' => $data['max_attempts'] ?? 1,
                'shuffle_questions' => $data['shuffle_questions'] ?? false,
                'show_answers' => $data['show_answers'] ?? false,
            ]),
            'assignment' => $this->createAssignment($data),
            'material' => $this->createMaterial($data),
            default => throw new \InvalidArgumentException("Invalid content type: {$contentType}"),
        };
    }

    protected function createAssignment(array $data): Assignment
    {
        $assignment = Assignment::create([
            'instructions' => $data['instructions'] ?? null,
            'due_date' => $data['due_date'] ?? null,
            'max_score' => $data['max_score'] ?? 100,
            'allow_late_submission' => $data['allow_late_submission'] ?? false,
            'allowed_file_types' => $data['allowed_file_types'] ?? null,
            'max_file_size' => $data['max_file_size'] ?? null,
        ]);

        // Handle file uploads for assignment
        if (!empty($data['files'])) {
            foreach ($data['files'] as $file) {
                $assignment->addMedia($file)->toMediaCollection('assignment_files');
            }
        }

        return $assignment;
    }

    protected function createMaterial(array $data): Material
    {
        $material = Material::create([
            'file_type' => $data['file_type'] ?? null,
            'is_downloadable' => $data['is_downloadable'] ?? true,
        ]);

        // Handle file upload for material
        if (!empty($data['file'])) {
            $material->addMedia($data['file'])->toMediaCollection('material_file');
        }

        return $material;
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
                // Handle file uploads for assignment
                if ($lessonContent->contentable instanceof Assignment && !empty($contentData['files'])) {
                    foreach ($contentData['files'] as $file) {
                        $lessonContent->contentable->addMedia($file)->toMediaCollection('assignment_files');
                    }
                    unset($contentData['files']);
                }

                // Handle file upload for material
                if ($lessonContent->contentable instanceof Material && !empty($contentData['file'])) {
                    $lessonContent->contentable->addMedia($contentData['file'])->toMediaCollection('material_file');
                    unset($contentData['file']);
                }

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

    public function getVideosByLessonId(string $lessonId): Collection
    {
        return $this->repository->query()
            ->where('lesson_id', $lessonId)
            ->where('contentable_type', VideoContent::class)
            ->with('contentable')
            ->orderBy('order')
            ->get();
    }

    public function getQuizzesByLessonId(string $lessonId): Collection
    {
        return $this->repository->query()
            ->where('lesson_id', $lessonId)
            ->where('contentable_type', Quiz::class)
            ->with('contentable')
            ->orderBy('order')
            ->get();
    }

    public function getAssignmentsByLessonId(string $lessonId): Collection
    {
        return $this->repository->query()
            ->where('lesson_id', $lessonId)
            ->where('contentable_type', Assignment::class)
            ->with('contentable')
            ->orderBy('order')
            ->get();
    }

    public function getMaterialsByLessonId(string $lessonId): Collection
    {
        return $this->repository->query()
            ->where('lesson_id', $lessonId)
            ->where('contentable_type', Material::class)
            ->with('contentable')
            ->orderBy('order')
            ->get();
    }

    public function lessonContentExists(string $id): bool
    {
        return $this->repository->exists($id);
    }

    public function getTotalCount(): int
    {
        return $this->repository->count();
    }

    public function getMaterialFiles(string $materialId): array
    {
        $material = Material::findOrFail($materialId);
        
        return $material->getMedia('material_file')->map(function ($media) {
            return [
                'id' => $media->id,
                'file_name' => $media->file_name,
                'file_url' => $media->getUrl(),
                'file_size' => $media->size,
                'file_type' => $media->mime_type,
            ];
        })->toArray();
    }
}
