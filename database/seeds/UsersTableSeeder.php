<?php

use Illuminate\Database\Seeder;
use App\Models\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // instantiate Faker object
        $faker = app(Faker\Generator::class);

        // fake avatar data
        $avatars = [
            'https://cdn.learnku.com/uploads/images/201710/14/1/s5ehp11z6s.png',
            'https://cdn.learnku.com/uploads/images/201710/14/1/Lhd1SHqu86.png',
            'https://cdn.learnku.com/uploads/images/201710/14/1/LOnMrqbHJn.png',
            'https://cdn.learnku.com/uploads/images/201710/14/1/xAuDMxteQy.png',
            'https://cdn.learnku.com/uploads/images/201710/14/1/ZqM7iaP4CR.png',
            'https://cdn.learnku.com/uploads/images/201710/14/1/NDnzMutoxX.png',
        ];

        // create data collection
        $users = factory(User::class)->times(10)->make()->each(function($user, $index) use ($faker, $avatars){
            // choose any image from $avatars array at random
            $user->avatar = $faker->randomElement($avatars);
        });

        // make model's hidden properties visible, and convert data collection to array
        $user_array = $users->makeVisible(['password', 'remember_token'])->toArray();

        // insert into database
        User::insert($user_array);

        // change the first user's data
        $user = User::find(1);
        $user->name = 'John';
        $user->email = 'john@abc.com';
        $user->avatar = 'https://cdn.learnku.com/uploads/images/201710/14/1/ZqM7iaP4CR.png';
        $user->save();

        // Initialize user roles, set user with id 1 as Founder
        $user->assignRole('Founder');

        // Set user with id 2 as Maintainer
        $user = User::find(2);
        $user->assignRole('Maintainer');
    }
}
