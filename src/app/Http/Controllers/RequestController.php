<?php

namespace App\Http\Controllers;

use App\Enums\RequestStates;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RequestController extends Controller
{
    public function index()
    {
        $auth_user = Auth::user();
        $requests = \App\Models\Request::where("state", RequestStates::PENDING)->get()->filter(function ($request) use ($auth_user) {
            return $request->currentSignee()->user_id == $auth_user->id;
        });
        return view("request.index", ["requests" => $requests]);
    }

    public function update(Request $request, $request_id)
    {
        $this_request = \App\Models\Request::find($request_id);
        $new_status = $request->json("state");
        if ($new_status == RequestStates::APPROVED) {
           if ($this_request->setNextSignee()) {
              echo DB::table($this_request->table_name)->where(["admission_id" => $this_request->primary_key])
                  ->update(json_decode($this_request->payload, true));
           }
        }
        return response("OK");
    }
}
