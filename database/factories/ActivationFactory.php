<?php

use Faker\Generator as Faker;

$factory->define(App\Entities\Activation::class, function (Faker $faker) {
    return [
        'code' => str_random(30),
        'completed_at' => date('Y-m-d H:i:s'),
    ];
});
