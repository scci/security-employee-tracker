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

$factory->define(SET\User::class, function (Faker\Generator $faker) {
    return [
        'emp_num'    => $faker->unique()->randomNumber(4),
        'first_name' => $faker->firstName,
        'nickname'   => $faker->firstName,
        'last_name'  => $faker->lastName,
        'email'      => $faker->email,
        'phone'      => $faker->phoneNumber,
        'status'     => 'active',
        'username'   => $faker->unique()->userName,
        'password'   => $faker->password,
    ];
});

$factory->define(SET\Training::class, function (Faker\Generator $faker) {
    return [
       'name'        => $faker->text(5).' training '.$faker->text(15),
       'renews_in'   => $faker->randomElement($array = ['90', '180', '365']),
       'description' => $faker->text(),
   ];
});

$factory->define(SET\TrainingType::class, function (Faker\Generator $faker) {
    return [
        'name'        => $faker->text(5).' type '.$faker->text(15),
        'description' => $faker->text(120),
        'sidebar'     => rand(0, 1),
        'status'      => 1,
    ];
});

$factory->define(SET\Duty::class, function (Faker\Generator $faker) {
    return [
        'name'        => $faker->catchphrase,
        'cycle'       => 'weekly',
        'description' => $faker->text(),
        'has_groups'  => 0,
    ];
});

$factory->define(SET\Group::class, function (Faker\Generator $faker) {
    return [
       'name'        => $faker->text(5).' grp '.$faker->text(15),
       'closed_area' => 0,
   ];
});

$factory->define(SET\Log::class, function (Faker\Generator $faker) use ($factory) {
    return [
        'comment'   => $faker->text(),
        'author_id' => $factory->create(SET\User::class)->id,
        'user_id'   => $factory->create(SET\User::class)->id,
    ];
});

$factory->define(SET\News::class, function (Faker\Generator $faker) use ($factory) {
    return [
        'title'        => $faker->text(5).' news '.$faker->text(15),
        'description'  => $faker->text(),
        'author_id'    => $factory->create(SET\User::class)->id,
        'publish_date' => $faker->date(),
        'expire_date'  => null,
        'send_email'   => 0,
    ];
});

$factory->defineAs(SET\News::class, 'seeder', function (Faker\Generator $faker) use ($factory) {
    return [
        'title'        => $faker->text(5).' news '.$faker->text(rand(30, 90)),
        'description'  => $faker->text(),
        'author_id'    => $faker->randomDigitNotNull(),
        'publish_date' => $faker->date(),
        'expire_date'  => null,
        'send_email'   => rand(0, 1),
    ];
});

$factory->define(SET\Note::class, function (Faker\Generator $faker) use ($factory) {
    return [
        'title'     => $faker->text(5).' note '.$faker->text(30),
        'comment'   => $faker->text(),
        'author_id' => $factory->create(SET\User::class)->id,
        'user_id'   => $factory->create(SET\User::class)->id,
        'alert'     => 0,
        'private'   => 0,
    ];
});

$factory->defineAs(SET\Note::class, 'seeder', function (Faker\Generator $faker) use ($factory) {
    return [
        'title'     => $faker->text(5).' note '.$faker->text(15),
        'comment'   => $faker->text(),
        'author_id' => $faker->randomDigitNotNull(),
        'user_id'   => $faker->randomDigitNotNull(),
        'alert'     => rand(0, 1),
        'private'   => rand(0, 1),
    ];
});

$factory->define(SET\TrainingUser::class, function (Faker\Generator $faker) use ($factory) {
    return [
        'comment'        => $faker->text(),
        'author_id'      => $factory->create(SET\User::class)->id,
        'user_id'        => $factory->create(SET\User::class)->id,
        'training_id'    => $factory->create(SET\Training::class)->id,
        'completed_date' => $faker->date(),
        'due_date'       => $faker->date(),
    ];
});

$factory->define(SET\Travel::class, function (Faker\Generator $faker) use ($factory) {
    return [
        'comment'     => $faker->text(),
        'author_id'   => $factory->create(SET\User::class)->id,
        'user_id'     => $factory->create(SET\User::class)->id,
        'location'    => $faker->city.', '.$faker->stateAbbr,
        'leave_date'  => $faker->date(),
        'return_date' => $faker->date('Y-m-d', '+ 2 weeks'),
        'brief_date'  => $faker->date(),
    ];
});

$factory->defineAs(SET\Travel::class, 'seeder', function (Faker\Generator $faker) use ($factory) {
    return [
        'comment'     => $faker->text(),
        'author_id'   => $faker->randomDigitNotNull(),
        'user_id'     => $faker->randomDigitNotNull(),
        'location'    => $faker->city.', '.$faker->stateAbbr,
        'leave_date'  => $faker->date(),
        'return_date' => $faker->date('Y-m-d', '+ 2 weeks'),
        'brief_date'  => $faker->date(),
    ];
});

$factory->define(SET\Visit::class, function (Faker\Generator $faker) use ($factory) {
    return [
        'smo_code'        => $faker->word.$faker->randomNumber(),
        'comment'         => $faker->text(),
        'poc'             => $faker->name(),
        'phone'           => $faker->phoneNumber,
        'visit_date'      => $faker->date(),
        'expiration_date' => $faker->date('Y-m-d', '+ 2 Months'),
        'author_id'       => $factory->create(SET\User::class)->id,
        'user_id'         => $factory->create(SET\User::class)->id,
    ];
});

$factory->defineAs(SET\Visit::class, 'seeder', function (Faker\Generator $faker) use ($factory) {
    return [
        'smo_code'        => $faker->word.$faker->randomNumber(),
        'comment'         => $faker->text(),
        'poc'             => $faker->name(),
        'phone'           => $faker->phoneNumber,
        'visit_date'      => $faker->date(),
        'expiration_date' => $faker->date('Y-m-d', '+ 2 Months'),
        'author_id'       => $faker->randomDigitNotNull(),
        'user_id'         => $faker->randomDigitNotNull(),
    ];
});

$factory->define(SET\Setting::class, function (Faker\Generator $faker) {
    return [
        'key'   => $faker->word,
        'value' => $faker->word,
    ];
});
