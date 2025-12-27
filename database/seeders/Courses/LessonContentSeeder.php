<?php

namespace Database\Seeders\Courses;

use App\Features\Courses\Models\Assignment;
use App\Features\Courses\Models\Course;
use App\Features\Courses\Models\Lesson;
use App\Features\Courses\Models\LessonContent;
use App\Features\Courses\Models\Material;
use App\Features\Courses\Models\Quiz;
use App\Features\Courses\Models\QuizQuestion;
use App\Features\Courses\Models\QuizQuestionOption;
use App\Features\Courses\Models\VideoContent;
use App\Features\Grades\Models\Grade;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class LessonContentSeeder extends Seeder
{
    private array $lessonMapping = [
        'lessonOne' => 1,
        'lessonTwo' => 2,
        'lessonThree' => 3,
        'lessonFour' => 4,
        'lessonFive' => 5,
        'lessonSix' => 6,
        'lessonSeven' => 7,
        'lessonEghit' => 8,
    ];

    public function run(): void
    {
        $this->seedGrade4Term1LessonContent();
    }

    private function seedGrade4Term1LessonContent(): void
    {
        $grade = Grade::where('code', 'G4')->first();

        if (!$grade) {
            $this->command->warn("Grade 4 not found. Please run GradeSeeder first.");
            return;
        }

        $course = Course::where('grade_id', $grade->id)
            ->whereHas('term', function ($query) {
                $query->where('name', 'like', '%Term 1%')
                    ->orWhere('name', 'like', '%First%');
            })
            ->first();

        if (!$course) {
            $this->command->warn("No course found for Grade 4 Term 1. Please ensure course exists.");
            return;
        }

        $this->seedVideosFromJson($course);
        $this->seedAssignmentsFromFiles($course);
        $this->seedMaterialsFromFiles($course);
        $this->seedQuizzesFromJson($course);
        $this->seedFinalExamFromJson($course);
    }

    private function seedVideosFromJson(Course $course): void
    {
        $jsonPath = storage_path('app/private/PrimaryAllGrades/Grade4CompleteCurriculum/Term1/Videos/videos.json');

        if (!File::exists($jsonPath)) {
            $this->command->warn("Videos JSON file not found at: {$jsonPath}");
            return;
        }

        $videoData = json_decode(File::get($jsonPath), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->command->error("Invalid JSON in videos file: " . json_last_error_msg());
            return;
        }

        DB::transaction(function () use ($videoData, $course) {
            foreach ($videoData as $lessonKey => $videoIds) {
                $lessonOrder = $this->lessonMapping[$lessonKey] ?? null;

                if (!$lessonOrder) {
                    $this->command->warn("Unknown lesson key: {$lessonKey}");
                    continue;
                }

                $lesson = Lesson::where('course_id', $course->id)
                    ->where('order', $lessonOrder)
                    ->first();

                if (!$lesson) {
                    $this->command->warn("Lesson {$lessonOrder} not found for course {$course->id}");
                    continue;
                }

                $videoCount = 0;
                foreach ($videoIds as $index => $videoId) {
                    $existingVideo = VideoContent::where('video_url', $videoId)->first();

                    if ($existingVideo) {
                        $existingVideoContent = LessonContent::where('lesson_id', $lesson->id)
                            ->where('contentable_type', VideoContent::class)
                            ->where('contentable_id', $existingVideo->id)
                            ->first();

                        if ($existingVideoContent) {
                            continue;
                        }
                    }

                    $video = VideoContent::create([
                        'video_url' => $videoId,
                        'video_provider' => 'bunny',
                        'duration' => 0,
                        'thumbnail_url' => null,
                    ]);

                    LessonContent::create([
                        'lesson_id' => $lesson->id,
                        'content_type' => 'video',
                        'contentable_type' => VideoContent::class,
                        'contentable_id' => $video->id,
                        'title' => "Video " . ($index + 1) . " - Lesson {$lessonOrder}",
                        'description' => "Video content for lesson {$lessonOrder}",
                        'order' => $index + 1,
                        'duration' => 0,
                        'is_required' => true,
                        'is_published' => true,
                    ]);

                    $videoCount++;
                }

                if ($videoCount > 0) {
                    $this->command->info("Created {$videoCount} videos for Lesson {$lessonOrder}");
                }
            }
        });
    }

    private function seedQuizzesFromJson(Course $course): void
    {
        $jsonPath = storage_path('app/private/PrimaryAllGrades/Grade4CompleteCurriculum/Term1/Quizzes/PerSession/QuizzePerSession.json');

        if (!File::exists($jsonPath)) {
            $this->command->warn("Quiz JSON file not found at: {$jsonPath}");
            return;
        }

        $quizData = json_decode(File::get($jsonPath), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->command->error("Invalid JSON in quiz file: " . json_last_error_msg());
            return;
        }

        DB::transaction(function () use ($quizData, $course) {
            foreach ($quizData as $lessonKey => $questions) {
                $lessonOrder = $this->lessonMapping[$lessonKey] ?? null;

                if (!$lessonOrder) {
                    $this->command->warn("Unknown lesson key: {$lessonKey}");
                    continue;
                }

                $lesson = Lesson::where('course_id', $course->id)
                    ->where('order', $lessonOrder)
                    ->first();

                if (!$lesson) {
                    $this->command->warn("Lesson {$lessonOrder} not found for course {$course->id}");
                    continue;
                }

                $existingQuizContent = LessonContent::where('lesson_id', $lesson->id)
                    ->where('contentable_type', Quiz::class)
                    ->where('title', "Session Quiz - Lesson {$lessonOrder}")
                    ->first();

                if ($existingQuizContent) {
                    $this->command->info("Quiz for Lesson {$lessonOrder} already exists, skipping...");
                    continue;
                }

                $quiz = Quiz::create([
                    'time_limit' => 15,
                    'passing_score' => 60,
                    'max_attempts' => 3,
                    'shuffle_questions' => true,
                    'show_answers' => true,
                ]);

                LessonContent::create([
                    'lesson_id' => $lesson->id,
                    'content_type' => 'quiz',
                    'contentable_type' => Quiz::class,
                    'contentable_id' => $quiz->id,
                    'title' => "Session Quiz - Lesson {$lessonOrder}",
                    'description' => "Quiz for lesson {$lessonOrder}",
                    'order' => 100,
                    'duration' => 15,
                    'is_required' => true,
                    'is_published' => true,
                ]);

                foreach ($questions as $questionData) {
                    $question = QuizQuestion::create([
                        'quiz_id' => $quiz->id,
                        'question' => $questionData['question'],
                        'type' => $questionData['type'],
                        'points' => $questionData['points'],
                        'order' => $questionData['order'],
                        'explanation' => $questionData['explanation'] ?? null,
                        'is_active' => true,
                    ]);

                    foreach ($questionData['options'] as $index => $optionData) {
                        QuizQuestionOption::create([
                            'question_id' => $question->id,
                            'option_text' => $optionData['text'],
                            'is_correct' => $optionData['is_correct'],
                            'order' => $index + 1,
                        ]);
                    }
                }

                $this->command->info("Created quiz for Lesson {$lessonOrder} with " . count($questions) . " questions");
            }
        });
    }

    private function seedAssignmentsFromFiles(Course $course): void
    {
        $assignmentPath = storage_path('app/private/PrimaryAllGrades/Grade4CompleteCurriculum/Term1/assgiment');

        if (!File::isDirectory($assignmentPath)) {
            $this->command->warn("Assignments folder not found at: {$assignmentPath}");
            return;
        }

        $assignmentFiles = [
            1 => 'Homework G04-Sem1-S01.pdf',
            2 => 'Homework G04-Sem1-S02.pdf',
            3 => 'Homework G04-Sem1-S03.pdf',
            4 => 'Homework G04-Sem1-S04.pdf',
            5 => 'Homework G04-Sem1-S05.pdf',
            6 => 'Homework G04-Sem1-S06.pdf',
            7 => 'Homework G04-Sem1-S07.pdf',
            8 => 'Homework G04-Sem1-S08.pdf',
        ];

        DB::transaction(function () use ($assignmentPath, $assignmentFiles, $course) {
            foreach ($assignmentFiles as $lessonOrder => $fileName) {
                $filePath = $assignmentPath . DIRECTORY_SEPARATOR . $fileName;

                if (!File::exists($filePath)) {
                    $this->command->warn("Assignment file not found: {$fileName}");
                    continue;
                }

                $lesson = Lesson::where('course_id', $course->id)
                    ->where('order', $lessonOrder)
                    ->first();

                if (!$lesson) {
                    $this->command->warn("Lesson {$lessonOrder} not found for course {$course->id}");
                    continue;
                }

                $existingAssignment = LessonContent::where('lesson_id', $lesson->id)
                    ->where('contentable_type', Assignment::class)
                    ->where('title', "Homework - Lesson {$lessonOrder}")
                    ->first();

                if ($existingAssignment) {
                    continue;
                }

                $assignment = Assignment::create([
                    'instructions' => "Complete the homework assignment for Lesson {$lessonOrder}",
                    'due_date' => now()->addDays(7),
                    'max_score' => 100,
                    'allow_late_submission' => true,
                    'allowed_file_types' => ['pdf', 'doc', 'docx'],
                    'max_file_size' => 10240,
                ]);

                $assignment->addMedia($filePath)
                    ->preservingOriginal()
                    ->toMediaCollection('assignment_files');

                LessonContent::create([
                    'lesson_id' => $lesson->id,
                    'content_type' => 'assignment',
                    'contentable_type' => Assignment::class,
                    'contentable_id' => $assignment->id,
                    'title' => "Homework - Lesson {$lessonOrder}",
                    'description' => "Homework assignment for lesson {$lessonOrder}",
                    'order' => 50,
                    'duration' => 30,
                    'is_required' => true,
                    'is_published' => true,
                ]);

                $this->command->info("Created assignment for Lesson {$lessonOrder}");
            }
        });
    }

    private function seedMaterialsFromFiles(Course $course): void
    {
        $materialsPath = storage_path('app/private/PrimaryAllGrades/Grade4CompleteCurriculum/Term1/matrials');

        if (!File::isDirectory($materialsPath)) {
            $this->command->warn("Materials folder not found at: {$materialsPath}");
            return;
        }

        $materialFiles = [
            1 => 'G04-S01.pptx',
            2 => 'G04-S02.pptx',
            3 => 'G04-S03.pptx',
            4 => 'G04-S04.pptx',
            5 => 'G4-S05.pptx',
            6 => 'G4-S06.pptx',
            7 => 'G4-S07.pptx',
            8 => 'G4-S08.pptx',
        ];

        DB::transaction(function () use ($materialsPath, $materialFiles, $course) {
            foreach ($materialFiles as $lessonOrder => $fileName) {
                $filePath = $materialsPath . DIRECTORY_SEPARATOR . $fileName;

                if (!File::exists($filePath)) {
                    $this->command->warn("Material file not found: {$fileName}");
                    continue;
                }

                $lesson = Lesson::where('course_id', $course->id)
                    ->where('order', $lessonOrder)
                    ->first();

                if (!$lesson) {
                    $this->command->warn("Lesson {$lessonOrder} not found for course {$course->id}");
                    continue;
                }

                $existingMaterial = LessonContent::where('lesson_id', $lesson->id)
                    ->where('contentable_type', Material::class)
                    ->where('title', "Presentation - Lesson {$lessonOrder}")
                    ->first();

                if ($existingMaterial) {
                    continue;
                }

                $material = Material::create([
                    'file_type' => 'presentation',
                    'is_downloadable' => true,
                ]);

                $material->addMedia($filePath)
                    ->preservingOriginal()
                    ->withCustomProperties(['skip_size_check' => true])
                    ->toMediaCollection('material_file', 'media');

                LessonContent::create([
                    'lesson_id' => $lesson->id,
                    'content_type' => 'material',
                    'contentable_type' => Material::class,
                    'contentable_id' => $material->id,
                    'title' => "Presentation - Lesson {$lessonOrder}",
                    'description' => "Presentation slides for lesson {$lessonOrder}",
                    'order' => 0,
                    'duration' => 0,
                    'is_required' => false,
                    'is_published' => true,
                ]);

                $this->command->info("Created material for Lesson {$lessonOrder}");
            }
        });
    }

    private function seedFinalExamFromJson(Course $course): void
    {
        $jsonPath = storage_path('app/private/PrimaryAllGrades/Grade4CompleteCurriculum/Term1/Exam/exam.json');

        if (!File::exists($jsonPath)) {
            $this->command->warn("Final exam JSON file not found at: {$jsonPath}");
            return;
        }

        $examData = json_decode(File::get($jsonPath), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->command->error("Invalid JSON in final exam file: " . json_last_error_msg());
            return;
        }

        if ($course->final_quiz_id) {
            $this->command->info("Final exam already exists for course, skipping...");
            return;
        }

        DB::transaction(function () use ($examData, $course) {
            $quiz = Quiz::create([
                'time_limit' => 60,
                'passing_score' => 70,
                'max_attempts' => 1,
                'shuffle_questions' => true,
                'show_answers' => false,
            ]);

            $course->update(['final_quiz_id' => $quiz->id]);

            foreach ($examData as $questionData) {
                $question = QuizQuestion::create([
                    'quiz_id' => $quiz->id,
                    'question' => $questionData['question'],
                    'type' => $questionData['type'],
                    'points' => $questionData['points'],
                    'order' => $questionData['order'],
                    'explanation' => $questionData['explanation'] ?? null,
                    'is_active' => true,
                ]);

                if (isset($questionData['options'])) {
                    foreach ($questionData['options'] as $index => $optionData) {
                        QuizQuestionOption::create([
                            'question_id' => $question->id,
                            'option_text' => $optionData['text'],
                            'is_correct' => $optionData['is_correct'],
                            'order' => $index + 1,
                        ]);
                    }
                }
            }

            $this->command->info("Created Final Exam for course with " . count($examData) . " questions");
        });
    }
}
