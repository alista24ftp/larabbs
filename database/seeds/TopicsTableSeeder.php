<?php

use Illuminate\Database\Seeder;
use App\Models\Topic;
use App\Models\User;
use App\Models\Category;

class TopicsTableSeeder extends Seeder
{
    public function run()
    {
        // Array containing all users' IDs, eg. [1,2,3,4]
        $user_ids = User::all()->pluck('id')->toArray();

        // Array containing all categories' IDs, eg. [1,2,3,4]
        $category_ids = Category::all()->pluck('id')->toArray();

        // instantiate Faker
        $faker = app(Faker\Generator::class);

        $topics = factory(Topic::class)->times(100)->make()->each(function ($topic, $index) use ($user_ids, $category_ids, $faker) {
            // randomly select a user ID and a category ID from corresponding arrays
            $topic->user_id = $faker->randomElement($user_ids);
            $topic->category_id = $faker->randomElement($category_ids);
        });

        Topic::insert($topics->toArray());
    }

}

