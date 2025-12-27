<?php

namespace Database\Seeders\Courses;

use App\Features\Courses\Models\Course;
use App\Features\Courses\Models\Lesson;
use Illuminate\Database\Seeder;

class LessonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $cousreId = Course::where('title', 'Introduction to computer and programming Course')->first()->id;

        $lessons = [
            // Python Course lessons
            [
                'course_id' => $cousreId,
                'title' => ' Introduction to Computers',
                'description' => 'This session introduces students to the fundamental concepts of computer systems. They will explore what computers are, identify their tasks, and distinguish between hardware (input/output devices, internal parts) and software. The session also clarifies the roles of "Users" versus "Programmers" through interactive class activities where students identify computer components in real-life scenarios.',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'course_id' => $cousreId,
                'title' => 'Introduction to Block Coding',
                'description' => 'This session moves from theory to practice by introducing programming basics. Students learn what a program is and the difference between block-based and text-based languages. They get familiar with the Pictoblox interface, learning to manage sprites, backdrops, and basic blocks. Activities focus on understanding "Sequence" by translating real-life steps into code.',
                'order' => 2,
                'is_active' => true,
            ],
            [
                'course_id' => $cousreId,
                'title' => 'Variables in Programming',
                'description' => 'Students delve into storing data using variables. The session explains why variables are necessary (e.g., for keeping score) and covers variable types (numbers and strings). Activities include initializing variables, changing their values, and using them to track game states like "Score" or "Player Name."',
                'order' => 3,
                'is_active' => true,
            ],
            [
                'course_id' => $cousreId,
                'title' => 'Events and If Conditions',
                'description' => 'This session covers interactivity and decision-making. Students learn about Events (triggers like key presses or clicking) and X-Y coordinates for movement. The session introduces "If" conditions, enabling the program to make decisions based on specific scenarios, such as detecting collisions in a "Maze game."',
                'order' => 4,
                'is_active' => true,
            ],
            [
                'course_id' => $cousreId,
                'title' => 'Loops in Programming',
                'description' => 'This session focuses on the concept of repetition. Students explore different types of loops: counting loops (Repeat number), conditional loops (Repeat until), and infinite loops (Forever). Through the "Space War" game activity, students learn when to apply each loop type to efficiently automate repetitive tasks like background music or movement.',
                'order' => 5,
                'is_active' => true,
            ],
            [
                'course_id' => $cousreId,
                'title' => 'User Inputs',
                'description' => 'Focusing on interaction, this session teaches how to accept and process data from the user. Students learn to use "Ask and Wait" blocks and store the input in the "Answer" variable. Activities include creating a "Robot Assistant" that can ask questions, join text strings, and perform calculations based on user input.',
                'order' => 6,
                'is_active' => true,
            ],
            [
                'course_id' => $cousreId,
                'title' => 'Operators',
                'description' => 'This session introduces the logic and math capabilities of programming. Students explore three types of operators: Mathematical (math operations), Comparing (greater than, less than, equal), and Logical (True/False operations). Through the "Shopping Assistant" activity, students use these operators to make complex decisions, such as checking if a shoe size fits.',
                'order' => 7,
                'is_active' => true,
            ],
            [
                'course_id' => $cousreId,
                'title' => 'Functions',
                'description' => 'The final session introduces advanced code organization using Functions ("My Blocks"). Students learn to define their own custom blocks to group commands, improving code readability and reusability. Activities include creating a custom "Jump" block and a "Dance" block that accepts arguments to control speed.',
                'order' => 8,
                'is_active' => true,
            ],
        ];

        foreach ($lessons as $lesson) {
            if ($lesson['course_id']) {
                Lesson::create($lesson);
            }
        }

        $this->command->info('✅ Lessons seeded successfully!');
    }
}
