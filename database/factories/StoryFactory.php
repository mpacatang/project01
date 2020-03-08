<?php

use Faker\Generator as Faker;

$factory->define(App\Entities\Story::class, function (Faker $faker) {
    return [
        'photo' => str_random(3) . '-' . date('dmYhis') . '.jpg',
        'caption' => $faker->sentence
    ];
});
