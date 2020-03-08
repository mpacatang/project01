<?php

use Faker\Generator as Faker;

$factory->define(App\Entities\User::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'username' => $faker->userName,
        'email' => $faker->unique()->safeEmail,
        'phone_number' => $faker->phoneNumber,
        'bio' => $faker->sentence,
        'photo' => str_random(3) . '-' . date('dmYhis') . '.jpg',
        'gender' => ['male', 'female'][$faker->numberBetween($min = 0, $max = 1)],
        'password' => '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', // secret
        'remember_token' => str_random(10),
    ];
});
