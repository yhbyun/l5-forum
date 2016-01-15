<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

use App\Node;
use App\User;
use Carbon\Carbon;

$factory->define(App\User::class, function ($faker) {
    return [
        'github_id'        => $faker->unique()->numberBetween(1, 100),
        'github_url'       => $faker->url(),
        'name'             => $faker->userName(),
        'twitter_account'  => $faker->userName(),
        'personal_website' => $faker->url(),
        'signature'        => $faker->sentence(),
        'introduction'     => $faker->sentence(),
        'email'            => $faker->email(),
    ];
});

$factory->defineAs(App\Node::class, 'topNode', function ($faker) {
    return [
        'name' => $faker->word()
    ];
});

$factory->define(App\Node::class, function ($faker) {
    $name = $faker->word();

    return [
        'name'        => $name,
        'slug'        => $name,
        'description' => $faker->sentence()
    ];
});

$factory->define(App\Topic::class, function ($faker) {
    return [
        'title'      => $faker->sentence(),
        'body'       => $faker->text(),
        'created_at' => $faker->dateTimeBetween('-1 month', '+3 days'),
        'updated_at' => $faker->dateTimeBetween('-1 month', '+3 days'),
    ];
});

$factory->defineAs(App\Topic::class, 'excellent', function ($faker) use ($factory) {
    $topic = $factory->raw(App\Topic::class);

    return array_merge($topic, ['is_excellent' => true]);
});

$factory->define(App\Reply::class, function ($faker) {
    return [
        'body' => $faker->text(),
    ];
});

$factory->define(App\Favorite::class, function ($faker) {
    return [];
});
