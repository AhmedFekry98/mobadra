<?php

namespace App\Features\Courses\Seeders;

use App\Features\Courses\Models\LessonContent;
use App\Features\Courses\Models\VideoQuiz;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FixVideoQuizzesSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Starting to fix video_quizzes video_content_id...');

        // Get all video quizzes that have wrong video_content_id (lesson_content_id instead of video_content_id)
        $quizzes = VideoQuiz::all();
        $fixed = 0;
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
                    // Delete duplicate - keep the existing one
                    $this->command->warn("Deleting duplicate quiz ID {$quiz->id} (video_content_id would be {$correctVideoContentId})");
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

        $this->command->info("Done! Fixed: {$fixed}, Deleted duplicates: {$deleted}, Skipped: {$skipped}");
    }
}
