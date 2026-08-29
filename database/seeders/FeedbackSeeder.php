<?php

namespace Database\Seeders;

use App\Models\Feedback;
use DB;
use Hash;
use Illuminate\Database\Seeder;
use Str;

class FeedbackSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userCount = DB::table('users')->count();
        if ($userCount < 50) {
            $usersToCreate = 50 - $userCount;
            $users = [];
            for ($i = 0; $i < $usersToCreate; $i++) {
                $users[] = [
                    'name' => 'User '.Str::random(5),
                    'email' => 'user'.Str::random(5).'@example.com',
                    'password' => Hash::make('password'), // not used directly
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            DB::table('users')->insert($users);
        }

        // 3. Neutral comment pool
        $sample_feedbacks = config('constants.sample_feedbacks');
        shuffle($sample_feedbacks);

        foreach ($sample_feedbacks as $fb) {
            $feedback = new Feedback;
            $feedback->restaurant_id = 1;
            $feedback->comment = $fb['comment'];
            $feedback->rating = $fb['rating'];
            $feedback->save();
        }

        $this->command->info('1000 neutral feedback entries seeded successfully.');
    }
}
