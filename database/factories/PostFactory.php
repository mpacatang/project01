<?php

use Faker\Generator as Faker;

$factory->define(App\Entities\Post::class, function (Faker $faker) {
    return [
    	'slug' => uniqid(),
        'photo' => str_random(3) . '-' . date('dmYhis') . '.jpg',
        'caption' => $faker->sentence,
        'created_at' => $faker->dateTimeBetween($startDate = '-1 years', $endDate = 'now')
    ];
});
