<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\GoogleCalender;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;


class DeleteGoogleCalendarEvent implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public User $user;
    public ?string $eventId;

    /**
     * Create a new job instance.
     */
    public function __construct(User $user, ?string $eventId)
    {
        $this->user = $user;
        $this->eventId = $eventId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if (!empty($this->eventId)) {
            try {
                $result = (new GoogleCalender($this->user))->deleteEvent($this->eventId);
                \Log::info("Google Calendar event deleted successfully", [
                    'user_id' => $this->user->id,
                    'user_email' => $this->user->email,
                    'event_id' => $this->eventId,
                    'result' => $result
                ]);
            } catch (\Exception $e) {
                \Log::error("Failed to delete Google Calendar event", [
                    'user_id' => $this->user->id,
                    'user_email' => $this->user->email,
                    'event_id' => $this->eventId,
                    'error' => $e->getMessage()
                ]);
                // Re-throw to mark job as failed so it can be retried
                throw $e;
            }
        } else {
            \Log::warning("DeleteGoogleCalendarEvent job called with empty event ID", [
                'user_id' => $this->user->id,
                'user_email' => $this->user->email
            ]);
        }
    }
}
