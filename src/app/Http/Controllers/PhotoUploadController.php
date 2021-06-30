<?php

namespace App\Http\Controllers;

use App\Enums\RequestTypes;
use App\Models\RequestModel;
use App\Models\Student;
use Illuminate\Support\Facades\Auth;
use App\Services\PhotoUploadService;

class PhotoUploadController extends Controller
{
    protected PhotoUploadService $photoUploadService;

    public function __construct(PhotoUploadService $photoUploadService)
    {
        $this->photoUploadService = $photoUploadService;
    }

    public function create()
    {
        return view("photo_upload.create");
    }

    public function store(PhotoUploadService $request)
    {

        $authUser = Auth::user();
        $imageUrl = $this->photoUploadService->handleUploadedImage($request->image);

        if (!$authUser->student) {
            $authUser->getProfile()->updateProfileImage($imageUrl);
        } else {
            $requestId = RequestModel::createNewRequest(
                RequestTypes::STUDENT_PHOTO_UPLOAD,
                new Student(),
                $authUser->student_admission_id,
                ["photo_url" => $imageUrl],
                [$authUser->student->classroom->getRandomAdvisor()->user_id],
            );

            if ($requestId == null) {
                abort(400, "The last request for photo upload is still pending, please try again later");
            }
        }
        return redirect(route("uploadPhotoCreate"));
    }
}
