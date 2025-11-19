<?php

namespace Modules\Starup\Database\Seeders;

use Modules\Starup\Models\Badge;
use Modules\Starup\Models\BadgeCategory;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Modules\Starup\Models\BadgeRule;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class StarupDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $storagePath = storage_path('app/public/badges/');

        if (file_exists($storagePath)) {
            $files = glob($storagePath . '*');
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
        }


        Schema::disableForeignKeyConstraints();
        Badge::truncate();
        BadgeCategory::truncate();
        BadgeRule::truncate();

        Schema::enableForeignKeyConstraints();

        $this->seedCategories();
        $this->seedBadges();
    }

    private function seedCategories()
    {
        $categories = [
            [
                'name'  => 'Rating Based Badges',
                'slug'  => Str::slug('Rating Based Badges')
            ],
            [
                'name'  => 'Session Based Badges',
                'slug'  => Str::slug('Session Based Badges')
            ],
            [
                'name'  => 'Profile Verification Badges',
                'slug'  => Str::slug('Profile Verification Badges')
            ],
            [
                'name'  => 'Subject Mastery Badges',
                'slug'  => Str::slug('Subject Mastery Badges')
            ],
            [
                'name'  => 'Rehired Tutor Badges',
                'slug'  => Str::slug('Rehired Tutor Badges')
            ],
        ];

        if (!empty($categories)) {
            BadgeCategory::insert($categories);
        }
    }

    private function seedBadges()
    {
        $badges = [
            'Rating Based Badges' => [
                'badges' => [
                    [
                        'name' => 'Highly Rated Tutor',
                        'description' => '4.5 average rating with 60 reviews',
                        'image' => 'highly-rated-tutor.png',
                        'rules' => [
                            [
                                'criterion_type' => 'avg_rating',
                                'value' => 4.5
                            ],
                            [
                                'criterion_type' => 'total_reviews',
                                'value' => 30
                            ]
                        ]
                    ],
                    [
                        'name' => 'Quality Educator',
                        'description' => '4.8 average rating with 40 reviews',
                        'image' => 'quality-educator.png',
                        'rules' => [
                            [
                                'criterion_type' => 'avg_rating',
                                'value' => 4.8
                            ],
                            [
                                'criterion_type' => 'total_reviews',
                                'value' => 50
                            ]
                        ]
                    ],
                    [
                        'name' => 'Prime Tutor',
                        'description' => '5.0 average rating with 30 reviews',
                        'image' => 'prime-tutor.png',
                        'rules' => [
                            [
                                'criterion_type' => 'avg_rating',
                                'value' => 5.0
                            ],
                            [
                                'criterion_type' => 'total_reviews',
                                'value' => 100
                            ]
                        ]
                    ],
                ],
            ],
            'Session Based Badges' => [
                'badges' => [
                    [
                        'name' => 'Popular Tutor',
                        'description' => '50 sessions goal achieved',
                        'image' => 'popular-tutor.png',
                        'rules' => [
                            [
                                'criterion_type' => 'book_sessions_count',
                                'value' => 50
                            ]
                        ]
                    ],
                    [
                        'name' => 'Session Milestone (100 Sessions)',
                        'description' => '100 sessions goal achieved',
                        'image' => 'session-milestone.png',
                        'rules' => [
                            [
                                'criterion_type' => 'book_sessions_count',
                                'value' => 100
                            ]
                        ]
                    ],
                    [
                        'name' => 'Super Tutor',
                        'description' => '500 sessions goal achieved',
                        'image' => 'super-tutor.png',
                        'rules' => [
                            [
                                'criterion_type' => 'book_sessions_count',
                                'value' => 500
                            ]
                        ]
                    ],
                ],
            ],
            'Profile Verification Badges' => [
                'badges' => [
                    [
                        'name' => 'Profile Complete',
                        'description' => 'All profile fields filled',
                        'image' => 'profile-complete.png',
                        'rules' => [
                            [
                                'criterion_type' => 'profile_complete',
                                'value' => 1
                            ]
                        ]


                    ],
                    [
                        'name' => 'Trusted Educator',
                        'description' => 'Profile verified by admin',
                        'image' => 'trusted-educator.png',
                        'rules' => [
                            [
                                'criterion_type' => 'profile_complete',
                                'value' => true
                            ],
                            [
                                'criterion_type' => 'trusted_educator',
                                'value' => true
                            ],
                        ]
                    ],
                ],
            ],
            'Subject Mastery Badges' => [
                'badges' => [
                    [
                        'name' => 'Subject Specialist',
                        'description' => '50 sessions in one subject goal achieved',
                        'image' => 'subject-mastery-based-badges.png',
                        'rules' => [
                            [
                                'criterion_type' => 'completed_sessions_count',
                                'value' => 80
                            ]
                        ]
                    ],
                    [
                        'name' => 'Subject Master',
                        'description' => '100 sessions in one subject goal achieved',
                        'image' => 'mastering-math.png',
                        'rules' => [
                            [
                                'criterion_type' => 'book_sessions_count',
                                'value' => 100
                            ]
                        ]
                    ],
                    [
                        'name' => 'Subject Guru',
                        'description' => '500 sessions in one subject goal achieved',
                        'image' => 'subject-guru.png',
                        'rules' => [
                            [
                                'criterion_type' => 'book_sessions_count',
                                'value' => 500
                            ]
                        ]
                    ],
                ],
            ],
            'Rehired Tutor Badges' => [
                'badges' => [
                    [
                        'name' => 'Rehired Tutor',
                        'description' => 'Second session with the same student',
                        'image' => 'rehired-tutor.png',
                        'rules' => [
                            [
                                'criterion_type' => 'rehired_booking_count',
                                'value' => 1
                            ]
                        ]
                    ],
                    [
                        'name' => 'Returning Students',
                        'description' => 'Rehired by 10 students',
                        'image' => 'returning-students.png',
                        'rules' => [
                            [
                                'criterion_type' => 'rehired_booking_count',
                                'value' => 10
                            ]
                        ]
                    ],
                    [
                        'name' => 'Student Favorite',
                        'description' => 'Rehired by 25 students',
                        'image' => 'student-favorite.png',

                        'rules' => [
                            [
                                'criterion_type' => 'rehired_booking_count',
                                'value' => 25
                            ]
                        ]
                    ],
                ]
            ]
        ];

        if (!empty($badges)) {
            foreach ($badges as $category => $badges) {
                if (!empty($badges['badges'])) {
                    foreach ($badges['badges'] as $badge) {
                        $categoryId =  BadgeCategory::where('name', $category)->first()?->id;
                        if (empty($categoryId)) {
                            // dd($category);
                            continue;
                        }
                        $createdBadge = Badge::create([
                            'name' => $badge['name'],
                            'description' => $badge['description'],
                            'image' => $this->addImage($badge['image']),
                            'category_id' => $categoryId
                        ]);

                        if (!empty($badge['rules'])) {
                            foreach ($badge['rules'] as $rule) {
                                BadgeRule::create([
                                    'badge_id' => $createdBadge->id,
                                    'criterion_type' => $rule['criterion_type'],
                                    'criterion_value' => $rule['value']
                                ]);
                            }
                        }
                    }
                }
            }
        }
    }
    private function addImage($image)
    {
        if (!File::exists(storage_path('app/public/badges'))) {
            File::makeDirectory(storage_path('app/public/badges'), 0755, true, true);
        }

        if (File::exists(public_path('demo-content/badges/' . $image))) {
            Storage::disk(getStorageDisk())->putFileAs('badges', public_path('demo-content/badges/' . $image), $image);
            return 'badges/' . $image;
        } else {
            return null;
        }
    }
}
