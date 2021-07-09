<?php

namespace Database\Seeders;

use App\Models\Feedback;
use Illuminate\Database\Seeder;

class FeedbackSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // This is actually not connected to any student, just a dangling feedback for testing
        Feedback::factory([
            "faculty_id" => "hod_cse",
            "course_id" => 1,
        ])->create();
    }
}
