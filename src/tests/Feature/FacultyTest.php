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
        $random_existing_faculty_id = Faculty::all()->random()->id;
        $random_existing_phone = Faculty::all()->random()->phone;
        $random_existing_email = User::all()->random()->email;

        $new_faculty =  Faculty::factory()->make();
        $new_valid_faculty_id = $new_faculty->id;
        $new_valid_phone = $new_faculty->phone;
        $new_valid_email = User::factory()->make()->email;

        $invalid_data = [
            [],
            ["id" => $this->faker->userName, "email" => $this->faker->name,
                "phone" => $this->faker->numerify("##########"), "name" => $this->faker->name],
            ["id" => $this->faker->userName, "email" => $this->faker->email,
                "phone" => $this->faker->realText(10), "name" => $this->faker->name],
            ["id" => $random_existing_faculty_id, "email" => $new_valid_email,
                "phone" => $new_valid_phone, "name" => $this->faker->name],
            ["id" => $new_valid_faculty_id, "email" => $random_existing_email,
                "phone" => $new_valid_phone, "name" => $this->faker->name],
            ["id" => $new_valid_faculty_id, "email" => $new_valid_email,
                "phone" => $random_existing_phone, "name" => $this->faker->name],
        ];
        foreach ($invalid_data as $data) {
            $this->actingAs($this->pickRandomUser(Roles::HOD))->post("/faculty", $data)
            ->assertRedirect("/faculty/create");
        }

        // Admin has to provide a department manually
        $data = array_merge($new_faculty->toArray(), ["email" => $new_valid_email]);
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
        foreach ($this->users[Roles::FACULTY] as $faculty_user) {
            $url = sprintf("/faculty/%s", $faculty_user->faculty_id);
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
            $new_phone = Faculty::factory()->make()->phone;
            $new_email = User::factory()->make()->email;
            $other_faculty = Faculty::with("user")
                ->where("id", "!=", $faculty_user->faculty_id)->first();

            $this->actingAs($other_faculty->user)
                ->patch($url, ["phone" => $new_phone, "email" => $new_email])
                ->assertStatus(403);

            $valid_users = [$faculty_user, $this->pickRandomUser(Roles::ADMIN)];
            $this->actingAs($valid_users[array_rand($valid_users)])
                ->json("patch", $url, array("phone" => $new_phone, "email" => $new_email))
                ->assertStatus(200);

            $faculty_in_db = Faculty::where("user_id", $faculty_user->id)->first();
            $this->assertEquals($faculty_in_db->phone, $new_phone);
            $this->assertEquals($faculty_in_db->user->email, $new_email);
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
            $other_hod = Department::where("code", "!=", $faculty_user->faculty->department_code)->first()->getHOD();
            $this->actingAs($other_hod->user)->delete($url)->assertStatus(403);

            $valid_users = [$faculty_user->faculty->department->getHOD()->user, $this->pickRandomUser(Roles::ADMIN)];
            $this->actingAs($valid_users[array_rand($valid_users)])->delete($url)->assertStatus(200);
            $this->assertEquals(Faculty::find($faculty_user->faculty_id), null);
        }
    }
}
