<?php

use Faker\Generator as Faker;

$factory->define(App\Entities\Comment::class, function (Faker $faker) {
    return [
        'comment' => $faker->sentence
    ];
});
