<?php

use App\Node;
use App\User;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class TopicsTableSeeder extends Seeder
{

    public function run()
    {
        $faker = Faker::create();
        $users = User::lists('id')->all();
        $nodes = Node::lists('id')->all();

        foreach (range(1, 50) as $index) {
            factory(App\Topic::class)->create([
                'user_id' => $faker->randomElement($users),
                'node_id' => $faker->randomElement($nodes),
            ]);

            factory(App\Topic::class, 'excellent')->create([
                'user_id' => $faker->randomElement($users),
                'node_id' => $faker->randomElement($nodes),
            ]);
        }
    }
}
