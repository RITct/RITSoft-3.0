<?php

namespace Database\Seeders;

use App\Enums\Roles;
use App\Models\OfficeStaff;
use App\Models\User;
use Illuminate\Database\Seeder;

class OfficeStaffSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i = 0; $i < 2; $i++) {
            $user = User::factory()->create([
                "username" => sprintf("office_staff%d@rit.com", $i),
            ]);
            $office = OfficeStaff::factory()->create([
                "user_id" => $user->id,
            ]);
            $user->officeStaff()->associate($office)->save();
            $user->assignRole(Roles::OFFICE);
        }
    }
}
