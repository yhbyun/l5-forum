<?php

use Illuminate\Database\Seeder;

class NodesTableSeeder extends Seeder
{

    public function run()
    {
        factory(App\Node::class, 'topNode', 10)
            ->create()
            ->each(function ($node) {
                factory(App\Node::class, 10)->create(['parent_node' => $node->id]);
            });
    }
}
