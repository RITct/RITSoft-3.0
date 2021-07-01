<?php

namespace Tests\Feature;

use App\Enums\RequestStates;
use App\Enums\Roles;
use App\Models\RequestModel;
use App\Models\Student;
use App\Models\User;
use Tests\TestCase;

class RequestSystemTest extends TestCase
{
    private function createTestRequest()
    {
        $payload = ["name" => $this->faker->name];
        $studentUser = $this->pickRandomUser(Roles::STUDENT);
        $response = $this->actingAs($studentUser)
            ->post(route("testrequest.store"), $payload);

        $response->assertStatus(200);
        return ["id" => $response->json("id"), "payload" => $payload];
    }

    public function testTestRequestCreate()
    {
        $requestInDB = RequestModel::find($this->createTestRequest()["id"]);
        $this->assertNotNull($requestInDB);

        $positionRoleMap = [
            1 => Roles::STAFF_ADVISOR,
            2 => Roles::HOD,
            3 => Roles::PRINCIPAL
        ];

        foreach ($requestInDB->signees as $signee) {
            $this->assertTrue(User::find($signee->user_id)->hasRole($positionRoleMap[$signee->position]));
        }
    }

    private function changeRequestState(int $requestId, string $state, string $remark)
    {
        $requestInDB = RequestModel::find($requestId);
        $previousSignee = $requestInDB->currentSignee();
        $this->actingAs($requestInDB->currentSignee()->user)
            ->json(
                "PATCH",
                route("requests.update", ["request" => $requestId]),
                ["remark" => $remark, "state" => $state]
            )->assertStatus(200);

        $requestInDB->refresh();

        if ($state == RequestStates::REJECTED) {
            $this->assertEquals($state, $requestInDB->state);
            $this->assertEquals($remark, $requestInDB->currentSignee()->remark);
            $user = $requestInDB->currentSignee()->user;
            $status = 400;
        } else {
            $previousSignee->refresh();
            $this->assertEquals($remark, $previousSignee->remark);
            $this->assertEquals(RequestStates::APPROVED, $previousSignee->state);
            $this->assertEquals(RequestStates::PENDING, $requestInDB->state);
            $user = $previousSignee->user;
            $status = 403;
        }
        // State Pending will return 200 status
        $states = [RequestStates::APPROVED, RequestStates::REJECTED];
        foreach ($states as $state) {
            $this->actingAs($user)
                ->json(
                    "PATCH",
                    route("requests.update", ["request" => $requestId]),
                    ["state" => $state]
                )
                ->assertStatus($status);
        }
    }

    public function testRequestReject()
    {
        $this->changeRequestState(
            $this->createTestRequest()["id"],
            RequestStates::REJECTED,
            "rejected"
        );
    }

    public function testRequestApprove()
    {
        $this->changeRequestState(
            $this->createTestRequest()["id"],
            RequestStates::APPROVED,
            "approved"
        );
    }

    private function approveSignee($request)
    {
        $previousSignee = $request->currentSignee();
        $url = route("requests.update", ["request" => $request->id]);
        $remark = $this->faker->realText("10");
        $this->actingAs($request->currentSignee()->user)
            ->json(
                "PATCH",
                $url,
                ["state" => RequestStates::APPROVED, "remark" => $remark]
            )
            ->assertStatus(200);

        $previousSignee->refresh();
        $this->assertEquals(RequestStates::APPROVED, $previousSignee->state);
        $this->assertEquals($remark, $previousSignee->remark);
    }

    public function testFullFlow()
    {
        $requestData = $this->createTestRequest();
        $requestId = $requestData["id"];
        $payload = $requestData["payload"];
        $requestInDB = RequestModel::find($requestId);
        for ($i = 0; $i < 3; $i++) {
            $this->approveSignee($requestInDB);
            $requestInDB->refresh();
        }
        $this->assertEquals(RequestStates::APPROVED, $requestInDB->state);
        $this->assertEquals($payload["name"], Student::find($requestInDB->primary_value)->name);
    }
}
