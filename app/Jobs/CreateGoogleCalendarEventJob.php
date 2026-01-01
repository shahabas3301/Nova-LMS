<?php

namespace App\Jobs;

use App\Services\BookingService;
use App\Services\GoogleCalender;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;


class CreateGoogleCalendarEventJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $booking;

    /**
     * Create a new job instance.
     */
    public function __construct($booking)
    {
        $this->booking = $booking;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // STEP 1: Create TUTOR's calendar event (Slot Event)
        if (empty($this->booking->slot['meta_data']['event_id'])) {
            $tutorEventResponse = (new GoogleCalender($this->booking->bookee))->createEvent([
                'title'         => ($this->booking->orderItem->options['group_name'] . " " ?? '') . $this->booking->orderItem->title,
                'description'   => $this->booking->slot->description,
                'start_time'    => Carbon::parse($this->booking->start_time)->setTimezone(getUserTimezone($this->booking->bookee))->toIso8601String(),
                'end_time'      => Carbon::parse($this->booking->end_time)->setTimezone(getUserTimezone($this->booking->bookee))->toIso8601String()
            ]);
            
            // Save tutor's event ID
            $slotMeta               = $this->booking->slot->meta_data;
            $slotMeta['event_id']   = $tutorEventResponse['data']->id ?? null;
            (new BookingService())->updateSessionSlot($this->booking->slot, [ 'meta_data' => $slotMeta ]);
        }

        // STEP 2: Create STUDENT's calendar event (Booking Event)
        // Note: We don't have the link yet, BookingService::createMeetingLink will update it later
        $studentEventResponse = (new GoogleCalender($this->booking->booker))->createEvent([
            'title'         => $this->booking->orderItem->title . " " . $this->booking->tutor->full_name,
            'description'   => $this->booking->slot->description,
            'start_time'    => Carbon::parse($this->booking->start_time)->setTimezone(getUserTimezone($this->booking->booker))->toIso8601String(),
            'end_time'      => Carbon::parse($this->booking->end_time)->setTimezone(getUserTimezone($this->booking->booker))->toIso8601String()
        ]);
        
        // Save student's event ID
        $bookingMeta             = $this->booking->meta_data;
        $bookingMeta['event_id'] = $studentEventResponse['data']->id ?? null;
        (new BookingService())->updateBooking($this->booking, [ 'meta_data' => $bookingMeta ]);

        // STEP 3: Generate and Sync Google Meet Link
        // This generates the link on the Tutor's event and updates the Student's event with it
        try {
            (new BookingService())->createMeetingLink($this->booking);
            \Log::info("Google Meet link created and synced", [
                'booking_id' => $this->booking->id
            ]);
        } catch (\Exception $e) {
            \Log::error("Failed to generate meeting link", [
                'booking_id' => $this->booking->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}
