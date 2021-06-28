<?php

namespace App\Http\Controllers;

use App\Enums\RequestStates;
use App\Enums\RequestTypes;
use App\Models\RequestModel;
use App\Models\Student;
use Illuminate\Http\Request;
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

    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $auth_user = Auth::user();
        $imageUrl = $this->photoUploadService->handleUploadedImage($request->image);

        if (!$auth_user->student) {
            $auth_user->getProfile()->updateProfileImage($imageUrl);
        } else {
            $status = RequestModel::createNewRequest(
                RequestTypes::STUDENT_PHOTO_UPLOAD,
                new Student(),
                $auth_user->student_admission_id,
                ["photo_url" => $imageUrl],
                [$auth_user->student->classroom->getRandomAdvisor()->user_id],
            );

            if (!$status) {
                abort(400, "The last request for photo upload is still pending, please try again later");
            }
        }
        return redirect(route("uploadPhotoCreate"));
    }
}
