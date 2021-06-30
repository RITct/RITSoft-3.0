<?php

namespace Tests\Feature;

use App\Enums\Roles;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\User;
use Tests\TestCase;

class FacultyTest extends TestCase
{
    public function testIndex()
    {
        $this->assertLoginRequired("/faculty");

        $this->assertUsersOnEndpoint(
            "/faculty",
            "get",
            array(
                Roles::ADMIN => 200,
                Roles::HOD => 200,
                Roles::PRINCIPAL => 200,
                Roles::FACULTY => 403,
                Roles::STUDENT => 403,
            )
        );
    }

    public function testRetrieve()
    {

        foreach (Faculty::with("user")->get() as $faculty) {
            $url = sprintf("/faculty/%s", $faculty->id);
            $this->assertLoginRequired($url);

            $this->assertUsersOnEndpoint(
                $url,
                "get",
                array(
                    Roles::ADMIN => 200,
                    Roles::PRINCIPAL => 200
                )
            );

            $this->actingAs($faculty->user)->get($url)->assertStatus(200);
            $this->actingAs($faculty->department->getHOD()->user)->get($url)->assertStatus(200);

            $other_faculty = Faculty::where("id", "!=", $faculty->id)->get();
            $other_faculty = $other_faculty->first(function ($value, $_) {
                // Normal faculties have roles FACULTY &&|| STAFF_ADVISOR
                $count = $value->user->roles->count();
                return $count == 1 || ($value->user->hasRole(Roles::STAFF_ADVISOR) && $count == 2);
            });
            $other_hod = Department::where("code", "!=", $faculty->department_code)->first()->getHOD();
            $this->actingAs($other_faculty->user)->get($url)->assertStatus(403);
            $this->actingAs($other_hod->user)->get($url)->assertStatus(403);
        }
    }

    private function createAndAssertFaculty($user, $department = "CSE")
    {
        $faculty = Faculty::factory()->make();
        $email = User::factory()->make()->email;
        $data = array_merge($faculty->toArray(), ["email" => $email]);

        $this->actingAs($user)
            ->post("/faculty", $data)->assertRedirect("/faculty");

        $faculty_in_db = Faculty::find($faculty->id);
        $this->assertNotNull($faculty_in_db);
        if ($user->isAdmin()) {
            $this->assertEquals($faculty_in_db->department_code, $faculty->department_code);
        } else {
            $this->assertEquals($faculty_in_db->department_code, $department);
        }
    }

    public function testCreate()
    {
        $this->assertLoginRequired("/faculty/create");
        $this->assertLoginRequired("/faculty", "post");

        $this->assertUsersOnEndpoint(
            "/faculty/create",
            "get",
            array(
                Roles::ADMIN => 200,
                Roles::PRINCIPAL => 403,
                Roles::HOD => 200,
                Roles::FACULTY => 403,
                Roles::STUDENT => 403,
            )
        );
        $randomExistingFacultyId = Faculty::all()->random()->id;
        $randomExistingPhone = Faculty::all()->random()->phone;
        $randomExistingEmail = User::all()->random()->email;

        $newFaculty =  Faculty::factory()->make();
        $newValidFacultyId = $newFaculty->id;
        $newValidPhone = $newFaculty->phone;
        $newValidEmail = User::factory()->make()->email;

        $invalidData = [
            [],
            ["id" => $this->faker->userName, "email" => $this->faker->name,
                "phone" => $this->faker->numerify("##########"), "name" => $this->faker->name],
            ["id" => $this->faker->userName, "email" => $this->faker->email,
                "phone" => $this->faker->realText(10), "name" => $this->faker->name],
            ["id" => $randomExistingFacultyId, "email" => $newValidEmail,
                "phone" => $newValidPhone, "name" => $this->faker->name],
            ["id" => $newValidFacultyId, "email" => $randomExistingEmail,
                "phone" => $newValidPhone, "name" => $this->faker->name],
            ["id" => $newValidFacultyId, "email" => $newValidEmail,
                "phone" => $randomExistingPhone, "name" => $this->faker->name],
        ];
        foreach ($invalidData as $data) {
            $this->actingAs($this->pickRandomUser(Roles::HOD))->post("/faculty", $data)
            ->assertRedirect("/faculty/create");
        }

        // Admin has to provide a department manually
        $data = array_merge($newFaculty->toArray(), ["email" => $newValidEmail]);
        unset($data["department_code"]);
        $this->actingAs($this->pickRandomUser(Roles::ADMIN))->post(route("storeFaculty"), $data)
            ->assertRedirect(route("createFaculty"));

        foreach ($this->users[Roles::HOD] as $hod_user) {
            $this->createAndAssertFaculty($hod_user, $hod_user->faculty->department_code);
        }
        $this->createAndAssertFaculty($this->pickRandomUser(Roles::ADMIN));
    }

    public function testUpdate()
    {
        foreach ($this->users[Roles::FACULTY] as $facultyUser) {
            $url = sprintf("/faculty/%s", $facultyUser->faculty_id);
            $this->assertLoginRequired(sprintf("%s/edit", $url));
            $this->assertUsersOnEndpoint(
                $url,
                "get",
                array(
                    Roles::ADMIN => 200,
                    Roles::FACULTY => 200,
                    Roles::STUDENT => 403
                )
            );
            $newPhone = Faculty::factory()->make()->phone;
            $newEmail = User::factory()->make()->email;
            $otherFaculty = Faculty::with("user")
                ->where("id", "!=", $facultyUser->faculty_id)->first();

            $this->actingAs($otherFaculty->user)
                ->patch($url, ["phone" => $newPhone, "email" => $newEmail])
                ->assertStatus(403);

            $validUsers = [$facultyUser, $this->pickRandomUser(Roles::ADMIN)];
            $this->actingAs($validUsers[array_rand($validUsers)])
                ->json("patch", $url, array("phone" => $newPhone, "email" => $newEmail))
                ->assertStatus(200);

            $facultyInDb = Faculty::where("user_id", $facultyUser->id)->first();
            $this->assertEquals($facultyInDb->phone, $newPhone);
            $this->assertEquals($facultyInDb->user->email, $newEmail);
        }
    }

    public function testDestroy()
    {
        foreach ($this->users[Roles::FACULTY] as $faculty_user) {
            $url = sprintf("/faculty/%s", $faculty_user->faculty_id);
            $this->assertUsersOnEndpoint(
                $url,
                "delete",
                array(
                    Roles::PRINCIPAL => 403,
                    Roles::FACULTY => 403,
                    Roles::STUDENT => 403
                )
            );
            $otherHod = Department::where("code", "!=", $faculty_user->faculty->department_code)->first()->getHOD();
            $this->actingAs($otherHod->user)->delete($url)->assertStatus(403);

            $validUsers = [$faculty_user->faculty->department->getHOD()->user, $this->pickRandomUser(Roles::ADMIN)];
            $this->actingAs($validUsers[array_rand($validUsers)])->delete($url)->assertStatus(200);
            $this->assertEquals(Faculty::find($faculty_user->faculty_id), null);
        }
    }
}
