<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserNotificationsSeederTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;
    public function testSeederInsertsData()
    {
        $this->seed(\UsersTableSeeder::class);
        $this->seed(\UserNotificationsTableSeeder::class);

        $userCount = User::count();
        $notificationCount =UserNotification::count();

        $this->assertGreaterThan(0, $userCount);
        $this->assertGreaterThan(0, $notificationCount);
    }

    public function testNotificationJob()
    {
        $this->artisan('schedule:run', ['--quiet' => true]);

        $this->assertTrue(true);
    }
}
