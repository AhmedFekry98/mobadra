<?php

namespace App\Features\Courses\Seeders;

use App\Features\Courses\Models\LessonContent;
use App\Features\Courses\Models\VideoQuiz;
use App\Features\Courses\Models\VideoQuizQuestion;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FixVideoQuizzesSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Starting to fix video_quizzes...');

        $quizzes = VideoQuiz::with('questions')->get();
        $fixed = 0;
        $questionsMoved = 0;
        $deleted = 0;
        $skipped = 0;

        foreach ($quizzes as $quiz) {
            // Check if this video_content_id is actually a lesson_content_id
            $lessonContent = LessonContent::where('id', $quiz->video_content_id)
                ->where('contentable_type', 'App\\Features\\Courses\\Models\\VideoContent')
                ->first();

            if ($lessonContent) {
                $correctVideoContentId = $lessonContent->contentable_id;

                // Check if a quiz already exists for this video_content_id
                $existingQuiz = VideoQuiz::where('video_content_id', $correctVideoContentId)->first();

                if ($existingQuiz && $existingQuiz->id !== $quiz->id) {
                    // Move questions from this quiz to the existing correct quiz
                    $questionsCount = $quiz->questions->count();
                    if ($questionsCount > 0) {
                        VideoQuizQuestion::where('video_quiz_id', $quiz->id)
                            ->update(['video_quiz_id' => $existingQuiz->id]);
                        $this->command->info("Moved {$questionsCount} questions from quiz {$quiz->id} to quiz {$existingQuiz->id}");
                        $questionsMoved += $questionsCount;
                    }

                    // Delete the duplicate quiz
                    $this->command->warn("Deleting duplicate quiz ID {$quiz->id}");
                    $quiz->delete();
                    $deleted++;
                } else {
                    // Update to correct video_content_id
                    $quiz->video_content_id = $correctVideoContentId;
                    $quiz->save();
                    $this->command->info("Fixed quiz ID {$quiz->id}: {$lessonContent->id} -> {$correctVideoContentId}");
                    $fixed++;
                }
            } else {
                $skipped++;
            }
        }

        $this->command->info("Done! Fixed: {$fixed}, Questions moved: {$questionsMoved}, Deleted duplicates: {$deleted}, Skipped: {$skipped}");
    }
}
