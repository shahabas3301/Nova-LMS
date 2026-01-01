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
        // STEP 1: Create TUTOR's calendar event first (this generates the Google Meet link)
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
            
            // Extract the Google Meet link from tutor's event
            $googleMeetLink = $tutorEventResponse['data']->hangoutLink ?? null;
            
            if ($googleMeetLink) {
                \Log::info("Google Meet link generated for tutor", [
                    'booking_id' => $this->booking->id,
                    'tutor_id' => $this->booking->bookee->id,
                    'meet_link' => $googleMeetLink
                ]);
            }
        } else {
            // Tutor already has an event, try to get existing Meet link
            $googleMeetLink = $this->booking->slot->meta_data['meeting_link'] ?? null;
        }
        
        // STEP 2: Create STUDENT's calendar event with the SAME Google Meet link
        $studentEventResponse = (new GoogleCalender($this->booking->booker))->createEvent([
            'title'         => $this->booking->orderItem->title . " " . $this->booking->tutor->full_name,
            'description'   => $this->booking->slot->description . ($googleMeetLink ? "\n\nJoin Meeting: " . $googleMeetLink : ''),
            'start_time'    => Carbon::parse($this->booking->start_time)->setTimezone(getUserTimezone($this->booking->booker))->toIso8601String(),
            'end_time'      => Carbon::parse($this->booking->end_time)->setTimezone(getUserTimezone($this->booking->booker))->toIso8601String(),
            'meeting_link'  => $googleMeetLink // Pass the same Meet link to be added
        ]);
        
        // Save student's event ID
        $bookingMeta             = $this->booking->meta_data;
        $bookingMeta['event_id'] = $studentEventResponse['data']->id ?? null;
        
        // IMPORTANT: Save the Meet link in booking meta_data so it's accessible in UI
        if ($googleMeetLink) {
            $bookingMeta['meeting_link'] = $googleMeetLink;
            $bookingMeta['meeting_type'] = 'google_meet';
            
            \Log::info("Same Google Meet link assigned to student", [
                'booking_id' => $this->booking->id,
                'student_id' => $this->booking->booker->id,
                'meet_link' => $googleMeetLink
            ]);
        }
        
        (new BookingService())->updateBooking($this->booking, [ 'meta_data' => $bookingMeta ]);
    }
}
