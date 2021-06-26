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
            if (!RequestModel::isLastRequestIsPending(RequestTypes::STUDENT_PHOTO_UPLOAD, $auth_user->student_admission_id)) {
                RequestModel::createNewRequest(
                    RequestTypes::STUDENT_PHOTO_UPLOAD,
                    new Student(),
                    $auth_user->student_admission_id,
                    ["photo_url" => $imageUrl],
                    [[
                        "user_id" => $auth_user->student->classroom->staffAdvisors->first()->user_id,
                        "position" => 1
                    ]]
                );
            }
            else {
                $msg = sprintf(
                    "The last request for %s is still pending, please try again later",
                    RequestTypes::STUDENT_PHOTO_UPLOAD
                );
                abort(400, $msg);
            }
        }
        return redirect(route("uploadPhotoCreate"));
    }
}
