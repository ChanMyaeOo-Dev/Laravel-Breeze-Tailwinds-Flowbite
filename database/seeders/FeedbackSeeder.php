<?php

namespace Database\Seeders;

use DB;
use Hash;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
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
                    'name' => 'User ' . Str::random(5),
                    'email' => 'user' . Str::random(5) . '@example.com',
                    'password' => Hash::make('password'), // not used directly
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            DB::table('users')->insert($users);
        }

        // 3. Neutral comment pool
        $neutralComments = [
            'The food was okay, nothing special.',
            'Service was average, could be faster.',
            'Ambiance is decent, typical restaurant setting.',
            'Prices are reasonable for the portion size.',
            'I had the pasta, it was fine but not memorable.',
            'The staff was polite but not overly friendly.',
            'The restaurant was clean and well kept.',
            'My meal was served warm, not hot.',
            'The menu had a good variety, but the taste was average.',
            'I might come back if I’m in the area, but not a must-visit.',
            'The waiting time was acceptable.',
            'The dessert was okay, a bit too sweet for my taste.',
            'Portion sizes were moderate.',
            'The music was a little loud, but tolerable.',
            'The seating was comfortable enough.',
            'Nothing really stood out, but nothing was bad either.',
            'The food arrived promptly and was as described.',
            'It was an average dining experience overall.',
            'The flavors were mild, not very bold.',
            'I expected a bit more given the reviews, but it was fine.',
            'The restaurant was busy but we got a table quickly.',
            'The coffee was average, nothing special.',
            'The appetizers were good but the main course was just okay.',
            'The presentation was nice, but taste was standard.',
            'The staff seemed a bit indifferent but did their job.',
            'The price is fair for the quality you get.',
            'The place is clean and functional.',
            'I had a satisfactory meal, but not exceptional.',
            'The noise level was moderate.',
            'The food was neither great nor terrible.',
            'The service was okay, they forgot one item but corrected it.',
            'The restaurant is decent for a quick bite.',
            'The flavors were balanced but not exciting.',
            'It’s an okay place to eat, nothing to rave about.',
            'The menu is standard, with the usual options.',
            'The waitstaff was efficient but not engaging.',
            'The food was a bit bland, but edible.',
            'I had no complaints, but also no compliments.',
            'The atmosphere is typical for this type of restaurant.',
            'The meal filled me up, but I wouldn’t order it again.',
            'The place was clean and the service was okay.',
            'The food is consistent, but not outstanding.',
            'It’s a safe choice for a meal.',
            'The prices are average, and so is the food.',
            'The staff was busy but still attentive enough.',
            'The restaurant met my basic expectations.',
            'Nothing exceptional, but nothing wrong either.',
            'The food was fine, but I’ve had better.',
            'The dining experience was unremarkable.',
            'I would rate it as just okay.',
        ];

        // 4. Prepare feedback data in chunks for performance
        $chunkSize = 200;
        $totalFeedback = 1000;
        $now = Carbon::now();

        for ($i = 0; $i < $totalFeedback; $i += $chunkSize) {
            $feedbackBatch = [];
            $currentChunk = min($chunkSize, $totalFeedback - $i);

            for ($j = 0; $j < $currentChunk; $j++) {
                $randomDaysAgo = rand(0, 180); // up to ~6 months
                $randomHours = rand(0, 23);
                $randomMinutes = rand(0, 59);
                $createdAt = $now->copy()->subDays($randomDaysAgo)->setTime($randomHours, $randomMinutes);

                $feedbackBatch[] = [
                    'restaurant_id' => 1,
                    'rating' => rand(1, 5),
                    'comment' => $neutralComments[array_rand($neutralComments)],
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ];
            }

            DB::table('feedback')->insert($feedbackBatch);
        }

        $this->command->info('1000 neutral feedback entries seeded successfully.');
    }
}
