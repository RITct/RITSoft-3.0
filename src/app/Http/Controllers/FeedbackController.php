<?php

namespace App\Http\Controllers;

use App\Http\Requests\FeedbackRequest;
use App\Services\FeedbackService;

class FeedbackController extends Controller
{
    protected FeedbackService $service;

    public function __construct(FeedbackService $feedbackService)
    {
        $this->middleware("permission:feedback.list", ["only" => ["index"]]);
        $this->middleware("permission:feedback.retrieve", ["only" => ["show"]]);

        $this->service = $feedbackService;
    }

    public function index(FeedbackRequest $request)
    {
        return view("feedback.index", [
           "feedbacks" => $this->service->getAllFeedback($request->authUser)
        ]);
    }

    public function show(FeedbackRequest $request)
    {
        return view("feedback.retrieve", [
            "feedback" => $request->feedback
        ]);
    }
}
