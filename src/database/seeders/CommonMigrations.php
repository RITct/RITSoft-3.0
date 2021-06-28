<?php

/**
 * This file is supposed to be within migrations folder
 * But due to namespacing issues, it is now here
 */

namespace Database\Seeders;

use Illuminate\Database\Schema\Blueprint;

class CommonMigrations
{
    public static function definePersonalData(Blueprint $table)
    {
        $table->string('name', 50);
        $table->string('address', 200)->nullable();
        $table->string('phone', 13);
        $table->string("photo_url")->default("default_url");
    }
}
