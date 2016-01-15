<?php

use App\Topic;
use App\User;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class FavoritesTableSeeder extends Seeder
{

    public function run()
    {
        $faker = Faker::create();
        $users = User::lists('id')->all();
        $topics = Topic::lists('id')->all();

        foreach (range(1, 100) as $index) {
            factory(App\Favorite::class)->create([
                'user_id'  => $faker->randomElement($users),
                'topic_id' => $faker->randomElement($topics)
            ]);
        }
    }
}
