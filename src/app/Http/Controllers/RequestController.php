<?php

namespace App\Http\Controllers;

use App\Enums\RequestStates;
use App\Models\RequestModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RequestController extends Controller
{
    /**
     * GET mode: signee - view requests where user is signee, applicant - view requests where user is the applicant
     * GET state: RequestStates, filter queries based on state
     */
    public function index(Request $request)
    {
        $authUser = Auth::user();
        $mode = $request->input("mode", "signee");
        $state = $request->input("state", RequestStates::PENDING);
        if ($mode == "signee") {
            $requests = RequestModel::where("state", $state)->get()->filter(
                function ($request) use ($authUser) {
                    return $request->currentSignee()->user_id == $authUser->id || $authUser->isAdmin();
                }
            );
        } else {
            $profile = $authUser->getProfile();
            $requests = RequestModel::where([
                "primary_value" => $profile->getKey(),
                "state" => $state
            ])->get();
        }

        return view("request.index", ["requests" => $requests]);
    }

    public function update(Request $request, $request_id)
    {
        $thisRequest = RequestModel::findOrFail($request_id);
        $authUser = Auth::user();

        if ($thisRequest->currentSignee()->user_id != $authUser->id && !$authUser->isAdmin()) {
            abort(403);
        }

        if ($thisRequest->state != RequestStates::PENDING) {
            // Not even admin can bypass this, only PENDING requests can be updated
            abort(400, "This request is " . $thisRequest->state);
        }

        $newStatus = $request->json("state");
        $remark = $request->json("remark");

        if ($newStatus == RequestStates::APPROVED && $thisRequest->setNextSignee($remark)) {
            $thisRequest->performUpdation();
        } elseif ($newStatus == RequestStates::REJECTED) {
            $thisRequest->reject($remark);
        }

        return response("OK");
    }
}
