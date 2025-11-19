<?php

namespace Modules\Starup\Console\Commands;

use Modules\Starup\Models\Badge;
use Modules\Starup\Models\UserBadge;
use App\Casts\BookingStatus;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AssignUserBadges extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'assign:badges';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign badges to users';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tutors = User::select('id', 'email')
        ->whereHas('roles', fn($query)  => $query->whereName('tutor'))
        ->withCount('reviews')
        ->withAvg('reviews', 'rating')
        ->withCount([
                'bookingSlots' => fn($query) => $query->whereIn('status', [BookingStatus::$statuses['completed'], BookingStatus::$statuses['active']])
            ])

        ->with(['bookingSlots.booker', 'bookingSlots.slot.subjectGroupSubjects.subject', 'profile', 'languages', 'address', 'bookingSlots' => function($query) {
            $query->whereIn('status', [BookingStatus::$statuses['completed'], BookingStatus::$statuses['active']]);
        }])->get();

        $badges = Badge::with(['rules', 'category'])->get();

        if (empty($badges) || empty($tutors)) {
            UserBadge::truncate();
            return;
        }

        $assignBadges = [];
        
        foreach ($tutors as $tutor) {
            $tutorBadges = $this->getTutorBadges($badges, $tutor);
            $assignBadges = array_merge($assignBadges, $tutorBadges);
        }

        DB::beginTransaction();
        try {

            UserBadge::query()->delete();
            if (!empty($assignBadges)) {
                UserBadge::insert($assignBadges);
            }

            DB::commit();

            $this->info('Badges assigned successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error assigning badges to users', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            $this->error('Failed to assign badges to users');
        }
    
        
       
    }

    private function getTutorBadges($badges, $tutor)
    {

        if ($badges->isEmpty()) {
            return [];
        }
        
        $assignBadges = [];

        //Rating based badges
        $ratingBadges       = $badges->filter(fn($badge) => $badge->category?->slug === 'rating-based-badges');
        $ratingBadge        = $this->getRatingBadge($tutor, $ratingBadges);

        if (!empty($ratingBadge)) {
            $assignBadges[] = [
                'user_id' => $tutor->id, 
                'badge_id' => $ratingBadge,
                'created_at' => now(),
                'updated_at' => now()
            ];
        }

        //Session
        $sessionBadges      = $badges->filter(fn($badge) => $badge->category?->slug === 'session-based-badges'); 
        $sessionBadge       = $this->getSessionBadge($tutor, $sessionBadges);
        if (!empty($sessionBadge)) {
            $assignBadges[] = [
                'user_id' => $tutor->id, 
                'badge_id' => $sessionBadge,
                'created_at' => now(),
                'updated_at' => now()
            ];
        }

        //Profile verification badges
        $profileBadges      = $badges->filter(fn($badge) => $badge->category?->slug === 'profile-verification-badges');
        $profileBadge       = $this->getProfileBadge($tutor, $profileBadges);
        if (!empty($profileBadge)) {
            $assignBadges[] = [
                'user_id' => $tutor->id, 
                'badge_id' => $profileBadge,
                'created_at' => now(),
                'updated_at' => now()
            ];
        } 

        //Subject mastry badges
        $subjectMastryBadges = $badges->filter(fn($badge) => $badge->category?->slug === 'subject-mastery-badges');
        $subjectMastryBadge = $this->getSubjectMastryBadge($tutor, $subjectMastryBadges);
        if (!empty($subjectMastryBadge)) {
            $assignBadges[] = [
                'user_id' => $tutor->id, 
                'badge_id' => $subjectMastryBadge,
                'created_at' => now(),
                'updated_at' => now()
            ];
        }

         //Rehired tutor badges
         $rehiredTutorBadges = $badges->filter(fn($badge) => $badge->category?->slug === 'rehired-tutor-badges');
         $rehiredTutorBadge = $this->getRehiredTutorBadge($tutor, $rehiredTutorBadges);
         if (!empty($rehiredTutorBadge)) {
             $assignBadges[] = [
                 'user_id' => $tutor->id, 
                 'badge_id' => $rehiredTutorBadge,
                 'created_at' => now(),
                 'updated_at' => now()
             ];
         }

        $assignBadges = array_filter($assignBadges, function ($badge) {
            return !is_null($badge['badge_id']);
        });

        return $assignBadges;
    }

    private function getRatingBadge($tutor, $ratingBadges)
    {
        if (empty($ratingBadges)) {
            return null;
        }

        $ratingBadges = $ratingBadges
            ->sortByDesc(function ($badge) {
                return $badge->rules->filter(fn($rule) => $rule->criterion_type === 'avg_rating')->first()?->criterion_value;
            });

        if (!empty($ratingBadges)) {
            foreach ($ratingBadges as $badge) {
                $ratingRule = $badge->rules?->filter(fn($rule) => $rule->criterion_type === 'avg_rating')->first()?->criterion_value;
                $reviewsRule = $badge->rules?->filter(fn($rule) => $rule->criterion_type = 'total_reviews')->first()?->criterion_value;
                if (!empty($ratingRule) && $tutor->reviews_avg_rating >= $ratingRule && !empty($reviewsRule) && $tutor->reviews_count >= $reviewsRule) {
                    return $badge->id;
                }
            }
        }

        return null;
    }


    private function getSessionBadge($tutor, $badges) {
        if ($badges->isEmpty()) {
            return null;
        }

        $sessionBadges = $badges
            ->sortByDesc(function ($badge) {
                return $badge->rules->filter(fn($rule) => $rule->criterion_type === 'book_sessions_count')->first()?->criterion_value;
            });

        foreach ($sessionBadges as $badge) {
            $sessionCountRule = $badge->rules->filter(fn($rule) => $rule->criterion_type === 'book_sessions_count')->first()?->criterion_value;
            if (!empty($sessionCountRule) && $tutor->booking_slots_count >= $sessionCountRule) {
                return $badge->id;
            }
        }

    }

    private function getProfileBadge($tutor, $badges) {
        if ($badges->isEmpty()) {
            return null;
        }
        

        $trustedTutorBadge = $badges->filter(fn($badge) => $badge->rules->contains(fn($rule) => $rule->criterion_type === 'trusted_tutor'))?->first();
        if (!empty($trustedTutorBadge) && !empty($tutor->profile?->verified_at)) {
            return $trustedTutorBadge->id;
        }

        $completeProfileBadge = $badges->filter(fn($badge) => $badge->rules->contains(fn($rule) => $rule->criterion_type === 'profile_complete'))?->first();
        if (!empty($completeProfileBadge) && $this->isProfileCompleted($tutor)) {
            return $completeProfileBadge->id;
        }

        return null;
    }

    private function getSubjectMastryBadge($tutor, $badges)
    {
        if ($badges->isEmpty()) {
            return null;
        }

        $subjectBookingCounts = $tutor->bookingSlots
            ->groupBy(function ($bookingSlot) {
                return $bookingSlot->slot->subjectGroupSubjects->subject->id;
            })
            ->map(function ($group) {
                return $group->count();
            })->max();

        $subjectMastryBadges = $badges
            ->sortByDesc(function ($badge) {
                return $badge->rules->filter(fn($rule) => $rule->criterion_type === 'completed_sessions_count')->first()?->criterion_value;
            });


        foreach ($subjectMastryBadges as $badge) {
            $subjectMastryBadge = $badge->rules->filter(fn($rule) => $rule->criterion_type === 'completed_sessions_count')->first()?->criterion_value;
            if (!empty($subjectMastryBadge) && $subjectBookingCounts >= $subjectMastryBadge) {
                return $badge->id;
            }
        }
    }

    private function getRehiredTutorBadge($tutor, $badges) {

        $maxRecurringStudentsCount = $tutor->bookingSlots
        ->groupBy(function ($bookingSlot) {
            return $bookingSlot->booker->id;
        })
        ->map(function ($studentGroup) {
            return $studentGroup->count();
        })
        ->max();

        $rehiredTutorBadges = $badges
        ->sortByDesc(function ($badge) {
            return $badge->rules->filter(fn($rule) => $rule->criterion_type === 'rehired_booking_count')->first()?->criterion_value;
        });

        if (!empty($rehiredTutorBadges)) {
            foreach ($rehiredTutorBadges as $badge) {
                $rehiredBookingCountRule = $badge->rules?->filter(fn($rule) => $rule->criterion_type === 'rehired_booking_count')->first()?->criterion_value;
                if (!empty($rehiredBookingCountRule) && $maxRecurringStudentsCount >= $rehiredBookingCountRule) {
                    return $badge->id;
                }
            }
        }
        
        return null;
    }

    private function isProfileCompleted($tutor) {   
        return !empty($tutor->profile) &&
            !empty($tutor->profile->first_name) &&
            !empty($tutor->profile->last_name) &&
            !empty($tutor->email) &&
            (setting('_lernen.profile_phone_number') != 'yes' || !empty($tutor->profile?->phone_number)) &&
            !empty($tutor->profile->gender) &&
            !empty($tutor->profile->tagline) &&
            !empty($tutor->address) &&
            !empty($tutor->profile->native_language) &&
            !empty($tutor->languages) && 
            !empty($tutor->profile->description) &&
            !empty($tutor->profile->image) &&
            !empty($tutor->profile->intro_video);
    }

}

