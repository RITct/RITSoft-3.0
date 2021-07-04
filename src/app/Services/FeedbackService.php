<?php

namespace App\Services;

use App\Models\Feedback;

class FeedbackService
{
    public function getAllFeedback($authUser)
    {
        $query = Feedback::query();
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
}
