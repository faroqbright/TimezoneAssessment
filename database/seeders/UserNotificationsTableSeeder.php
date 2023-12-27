<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserNotification;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class UserNotificationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        $users = User::pluck('id')->toArray();

        foreach (range(1, 20) as $index) {
            $userId = $faker->randomElement($users);
            $user = User::find($userId);
            $userTimezone = $user->getTimezone();
            $scheduledAt = Carbon::createFromFormat('H:i', $faker->time('H:i'))
                ->setTimezone($userTimezone)
                ->format('H:i');
            $frequency = $faker->randomElement(['daily', 'weekly', 'monthly', 'custom']);

            UserNotification::insert([
                'user_id' => $userId,
                'scheduled_at' => $scheduledAt,
                'frequency' => $frequency,
                'notification_message' => "Hello ".$user->name.",\n\nThis is a notification for you. Your email address is: ".$user->email.".\nYou have a scheduled event at ". $scheduledAt." in your local timezone.\n\nThank you for using our notification system.\n\nBest regards,\nDemo Company",
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
