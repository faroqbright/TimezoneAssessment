<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\UserNotification;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $now = now();

        $notifications = UserNotification::where('scheduled_at', '<=', $now->format('H:i'))
            ->get();

        foreach ($notifications as $notification) {
            $user = User::find($notification->user_id);

            $scheduledTime = Carbon::createFromFormat('H:i', $notification->scheduled_at)
                ->setTimezone($user->getTimezone())
                ->format('H:i');

            switch ($notification->frequency) {
                case 'daily':
                    if (!$this->hasTriggeredToday($notification)) {
                        $this->sendNotification($user, $scheduledTime, $notification->notification_message);
                    }
                    break;
                case 'weekly':
                    if (!$this->hasTriggeredThisWeek($notification)) {
                        $this->sendNotification($user, $scheduledTime, $notification->notification_message);
                    }
                    break;
                case 'monthly':
                    if (!$this->hasTriggeredThisMonth($notification)) {
                        $this->sendNotification($user, $scheduledTime, $notification->notification_message);
                    }
                    break;
                case 'custom':
                    $this->sendNotification($user, $scheduledTime, $notification->notification_message);
                    break;
                default:
                    break;
            }
            UserNotification::where('id', $notification->id)
                ->update(['last_triggered_at' => now()]);
        }
    }

    private function hasTriggeredToday($notification)
    {
        $lastTriggeredAt = $notification->last_triggered_at ?? null;

        if ($lastTriggeredAt) {
            return Carbon::createFromFormat('Y-m-d H:i:s', $lastTriggeredAt)->isToday();
        }

        return false;
    }

    private function hasTriggeredThisWeek($notification)
    {
        $lastTriggeredAt = $notification->last_triggered_at ?? null;

        if ($lastTriggeredAt) {
            return Carbon::createFromFormat('Y-m-d H:i:s', $lastTriggeredAt)->isCurrentWeek();
        }

        return false;
    }

    private function hasTriggeredThisMonth($notification)
    {
        $lastTriggeredAt = $notification->last_triggered_at ?? null;

        if ($lastTriggeredAt) {
            return Carbon::createFromFormat('Y-m-d H:i:s', $lastTriggeredAt)->isCurrentMonth();
        }

        return false;
    }

    private function sendNotification($user, $scheduledTime, $message)
    {
        info("Notification sent to {$user->name} at {$scheduledTime}: {$message}");
    }
}
