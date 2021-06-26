<?php

namespace App\Http\Controllers;

use App\Enums\RequestStates;
use App\Models\RequestModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RequestController extends Controller
{
    public function index(Request $request)
    {
        $authUser = Auth::user();
        $state = $request->input("state", RequestStates::PENDING);
        $requests = RequestModel::where("state", $state)->get()->filter(
            function ($request) use ($authUser) {
                return $request->currentSignee()->user_id == $authUser->id || $authUser->isAdmin();
            }
        );
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
            $thisRequest->update(["state" => $newStatus, "remark" => $remark]);
        }

        return response("OK");
    }
}
