<?php

namespace App\Services;

use App\Enums\FeedbackQuestionType;
use App\Exceptions\IntendedException;
use App\Models\Course;
use App\Models\Feedback;

class FeedbackService
{
    public function getAllFeedback($authUser, $courseId = null)
    {
        $query = Feedback::query();
        if ($courseId) {
            $query = $query->where("course_id", $courseId);
        }
        if ($authUser->faculty?->isPrincipal() || $authUser->isAdmin()) {
            return $query->get();
        } elseif ($authUser->faculty?->isHOD()) {
            return $query->whereHas("course.classroom", function ($q) use ($authUser) {
                $q->where("department_code", $authUser->faculty->department_code);
            })->get();
        } else {
            return $query->where("faculty_id", $authUser->faculty_id)->get();
        }
    }

    private function validateFeedbackQuestionOrThrow($type, $answer): void
    {
        switch ($type) {
            case FeedbackQuestionType::MCQ:
                if (!is_numeric($answer) || !in_array($answer, [1, 2, 3, 4])) {
                    throw new IntendedException("Invalid Feedback");
                }
                break;
            case FeedbackQuestionType::BOOLEAN:
                if (!is_numeric($answer) || !in_array($answer, [0, 1])) {
                    throw new IntendedException("Invalid Feedback");
                }
                break;
            case FeedbackQuestionType::TEXT:
                // TODO Is validation required? YES
                break;
        }
    }

    public function combineFeedbackWithFormat(array $feedbackFromUser, Course $course): array
    {
        $format = $course->getFeedbackFormat();
        $feedbackResult = array();
        if (count($feedbackFromUser) > count($format)) {
            throw new IntendedException("Invalid Feedback");
        }
        for ($i = 0; $i < count($feedbackFromUser); $i++) {
            $this->validateFeedbackQuestionOrThrow($format[$i]["type"], $feedbackFromUser[$i]);
            array_push($feedbackResult, [
                "question" => $format[$i]["question"],
                "type" => $format[$i]["type"],
                "answer" => $feedbackFromUser[$i]
            ]);
            // Add option's text too if mcq
            if ($format[$i]["type"] == FeedbackQuestionType::MCQ) {
                $feedbackResult[$i]["answer_text"] = array_filter(
                    $format[$i]["options"],
                    function ($option) use ($feedbackFromUser, $i) {
                        return $option["score"] == $feedbackFromUser[$i];
                    }
                );
            }
        }
        return $feedbackResult;
    }

    public function verifyFaculties($data, $course): bool
    {
        // Check if faculty_ids are valid
        $userFacultyIds = array_keys($data);

        $validFacultyIds = $course->faculties->map(function ($faculty) {
            return $faculty->id;
        })->toArray();

        return count(array_diff($userFacultyIds, $validFacultyIds)) == 0;
    }

    public function isFeedbackComplete($authUser, $courseId)
    {
        return $authUser->student->curriculums->firstWhere("course_id", $courseId)->is_feedback_complete;
    }
}
