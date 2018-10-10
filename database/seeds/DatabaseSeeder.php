<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
//         $this->call(UsersTableSeeder::class);
        DB::table('users')->truncate();
        DB::table('designs')->truncate();
        DB::table('comments')->truncate();
        factory(App\User::class, 5)->create();
        factory(App\Design::class, 5)->create();
        factory(App\Comment::class, 5)->create();
    }
}
