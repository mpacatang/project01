<?php

use Faker\Generator as Faker;

$factory->define(App\Entities\MessageDetail::class, function (Faker $faker) {
    return [
    	'read' => 1,
        'message' => $faker->sentence
    ];
});
