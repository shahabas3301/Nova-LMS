<?php

namespace Modules\Forumwise\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Larabuild\Optionbuilder\Facades\Settings;
use App\Models\EmailTemplate;
use Modules\Forumwise\Models\Forum;
use Illuminate\Support\Facades\Cache;
use Modules\Forumwise\Models\Topic;
use Modules\Forumwise\Models\Media;
use Modules\Forumwise\Models\Comment;
use Modules\Forumwise\Models\TopicUser;
use Modules\Forumwise\Models\Category;

class ForumwiseDatabaseSeeder extends Seeder
{
    public function run()
    {
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Truncate forums, media, and topics tables
        Forum::truncate();
        Media::truncate();
        Topic::truncate();
        Category::truncate();
        TopicUser::truncate();   // Truncate related tables to prevent foreign key conflicts
        Comment::truncate();
        
        
        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $prefix = config('forumwise.db.prefix');

        $categories = [
            [
                'name' => 'Food',
                'label_color' => '#FFA500'
            ],
            [
                'name' => 'Marketing',
                'label_color' => '#1570EF'
            ],
            [
                'name' => 'Music',
                'label_color' => '#F44336'
            ],
        ];
        
        foreach ($categories as $category) {
            Category::create(
                [
                    'name' => $category['name'],
                    'label_color' => $category['label_color'] ?? null,
                ]
            );
        }
        
        // Forums data
        $forums = [
            [
                'title'                 => 'Food & Beverage',
                'slug'                  => Str::slug('Food & Beverage'),
                'description'           => 'A practical forum to take your cooking skills from dull to delicious',
                'status'                => 'active',
                'topic_role'            => json_encode(['tutor']),
                'category_id'           => 1,
                'created_by'            => 1,
            ],
            [
                'title'                 => 'Advertising',
                'slug'                  => Str::slug('Advertising'),
                'description'           => 'A practical forum to take your cooking skills from dull to delicious.',
                'status'                => 'active',
                'topic_role'            => json_encode(['tutor']),
                'category_id'           => 2,
                'created_by'            => 1,
            ],
            [
                'title'                 => 'Digital Marketing',
                'slug'                  => Str::slug('Digital Marketing'),
                'description'           => 'A practical forum to take your cooking skills from dull to delicious',
                'status'                => 'active',
                'topic_role'            => json_encode(['tutor']),
                'category_id'           => 2,
                'created_by'            => 1,
            ],
            [
                'title'                 => 'Social Media',
                'slug'                  => Str::slug('Social Media'),
                'description'           => 'A practical forum to take your cooking skills from dull to delicious',
                'status'                => 'active',
                'topic_role'            => json_encode(['tutor']),
                'category_id'           => 2,
                'created_by'            => 1,
            ],
            [
                'title'                 => 'Announcements',
                'slug'                  => Str::slug('Announcements'),
                'description'           => 'Official announcements from the administrators.',
                'status'                => 'active',
                'topic_role'            => json_encode(['tutor']),
                'category_id'           => 3,
                'created_by'            => 1,
            ]
        ];

        // Forum images
        $forumImages = [
            'marketing-1.png',
            'marketing-2.png',
            'marketing-3.png',
            'food.png',
            'marketing-2.png',
        ];

        // Forums seeding
        foreach ($forums as $index => $forumData) {
            $forum = Forum::create($forumData);

            if (isset($forumImages[$index])) {
                $randomNumber = Str::random(40);
                $imageName = $randomNumber . '_' . $forumImages[$index]; 
                $imagePath = 'forum/' . $imageName; 
        
                Storage::disk(getStorageDisk())->put($imagePath, file_get_contents(public_path('modules/forumwise/images/forums/' . $forumImages[$index])));
            } else {
                $imagePath = $this->storeImage('placeholder.png', 'forumwise/images/');
            }
    
            // Store the image in Media table
            Media::create([
                'mediaable_id'          => $forum->id,
                'mediaable_type'        => Forum::class,
                'type'                  => 'image',
                'path'                  => $imagePath,
                'created_at'            => now(),
                'updated_at'            => now(),
            ]);
        }

        // Topics data
        $topics = [
            [
                'title'                 => 'Tips for Perfecting Your Baking Skills',
                'slug'                  => Str::slug('Tips for Perfecting Your Baking Skills'),
                'description'           => 'Discover expert tips and techniques to elevate your baking game. Share your experiences, favorite recipes, and challenges as we explore the art of baking together!.',
                'status'                => 'active',
                'created_by'            => 2,
                'tags'                  => ['announcement', 'features'],
                'type'                  => 'public',
                'forum_id'              => '1',
            ],
            [
                'title'                 => 'What favorites food and or beverages do you enjoy',
                'slug'                  => Str::slug('What favorites food and or beverages do you enjoy'),
                'description'           => 'Share your favorite foods and drinks with the community and discover new favorites from others!',
                'status'                => 'active',
                'tags'                  => ['support', 'troubleshooting'],
                'type'                  => 'private',
                'forum_id'              => '1',
                'created_by'            => 2,
            ],
            [
                'title'                 => 'I cannot change the position of an absolutely positioned element',
                'slug'                  => Str::slug('I cannot change the position of an absolutely positioned element'),
                'description'           => 'Hi there and welcome to our brand new support forum.',
                'status'                => 'active',
                'tags'                  => ['announcement', 'features'],
                'type'                  => 'public',
                'forum_id'              => '1',
                'created_by'            => 1,
            ],
            [
                'title'                 => 'Figma Slide - Typography styles',
                'slug'                  => Str::slug('Figma Slide - Typography styles'),
                'description'           => 'Hi there and welcome to our brand new support forum. This is a place for our community (that’s you) to connect.',
                'status'                => 'active',
                'tags'                  => ['announcement', 'features'],
                'type'                  => 'public',
                'forum_id'              => '1',
                'created_by'            => 1,
            ]
        ];

        // Topic images
        $topicImages = [
            'topic-1.png',
            'topic-2.png',
            'topic-3.png',
            'topic-4.png',
        ];

        // Topics seeding
        foreach ($topics as $index => $topicData) {
            $topic = Topic::create($topicData);
        
            if (isset($topicImages[$index])) {
                $randomNumber = Str::random(40);
                $imageName = $randomNumber . '_' . $topicImages[$index];
                $imagePath = 'topic/' . $imageName; 
     
            }

            Storage::disk(getStorageDisk())->put($imagePath, file_get_contents(public_path('modules/forumwise/images/topics/' . $topicImages[$index])));

            Media::create([
                'mediaable_id'          => $topic->id,
                'mediaable_type'        => Topic::class,
                'type'                  => 'image',
                'path'                  => $imagePath,
                'created_at'            => now(),
                'updated_at'            => now(),
            ]);
        }

        $topicsUsers = [
            ['topic_id' => 1, 'user_id' => 2],
            ['topic_id' => 2, 'user_id' => 2],
            ['topic_id' => 3, 'user_id' => 1],
            ['topic_id' => 4, 'user_id' => 1],
            ['topic_id' => 1, 'user_id' => 3],
        ];

        DB::table('fw__topic_users')->insert($topicsUsers);

        $parentDescriptions = [
            'I recently tried sushi for the first time, and I was blown away by how fresh and flavorful it was! Anyone here a sushi fan? What’s your favorite roll?',
            'I’ve been on a smoothie kick lately. Does anyone have a favorite smoothie recipe? I’m looking for new ideas!',
        ];
        
        $childDescriptions = [
            'Sushi is amazing! My go-to roll is the spicy tuna roll, it has just the right amount of kick',
            'I absolutely love sushi! The salmon sashimi is always so fresh and melts in your mouth.',
            'Smoothies are a great way to start the day! My favorite is a banana and peanut butter smoothie with a bit of almond milk',
            'I’m really into green smoothies right now! Spinach, kale, pineapple, and a splash of coconut water. It’s refreshing and healthy.',
        ];
        
        $grandchildDescriptions = [
            'Spicy tuna is a classic! I love pairing it with some wasabi for extra heat. Do you make your own sushi at home?',
            'That sounds delicious! I’m more into the California roll myself. It’s simple, but so satisfying.',
            'Salmon sashimi is my favorite too! Have you tried it with soy sauce and a touch of lemon juice?.',
            'Sashimi is the best part! I like dipping it in a mix of soy sauce and a little bit of wasabi',
            'That sounds so good! I usually add a scoop of protein powder to mine for an extra boost.',
            'I’ve tried that combo before, it’s delicious! Sometimes I add a little honey for sweetness.',
            'Green smoothies are the best! I usually add a bit of ginger to mine for that extra zing.',
            'I love green smoothies too! Sometimes I mix in some chia seeds for added texture and nutrients.'

        ];

        for ($i = 1; $i <= 2; $i++) {
           
            $parentComment = Comment::create([
                'description' => $parentDescriptions[$i - 1], 
                'topic_id'    => 4,
                'created_by'  => $i,
                'parent_id'   => null,
            ]);
            $topicsUsers = [
                ['topic_id' => 4, 'user_id' => $i ,'status' => '3'],
            ];
            DB::table('fw__topic_users')->insert($topicsUsers);
            for ($j = 1; $j <= 2; $j++) {
            
                $childComment = Comment::create([
                    'description' => $childDescriptions[($i - 1) * 2 + ($j - 1)], 
                    'topic_id'    => 4,
                    'created_by'  => $j,
                    'parent_id'   => $parentComment->id,
                ]);
                $topicsUsers = [
                    ['topic_id' => 4, 'user_id' => $j ,'status' => '3'],
                ];
                DB::table('fw__topic_users')->insert($topicsUsers);
                for ($k = 1; $k <= 2; $k++) {
                  
                    Comment::create([
                        'description' => $grandchildDescriptions[($j - 1) * 2 + ($k - 1)], 
                        'topic_id'    => 4,
                        'created_by'  => $k,
                        'parent_id'   => $childComment->id,
                    ]);
                    $topicsUsers = [
                        ['topic_id' => 4, 'user_id' => $k ,'status' => '3'],
                    ];
                    DB::table('fw__topic_users')->insert($topicsUsers);
                }
            }
        }


        $def_setting = [
            '_forum_wise' => [
                'fw_heading'                       => 'Welcome to the Lernen Community Forum',
                'fw_paragraph'                     => 'Welcome to the Lernen Community Forum! This space is designed for learners and educators to connect, share ideas, ask questions, and grow together.',
                'fw_btn_txt'                       => 'Search',
                'fw_shape_image'                   => [uploadObMedia('modules/forumwise/images/banner/img-01.png')],
                'fw_left_shape_image'              => [uploadObMedia('modules/forumwise/images/banner/img-02.png')],
                'fw_right_shape_image'             => [uploadObMedia('modules/forumwise/images/banner/img-03.png')],
            ]
        ];
        if (!empty($def_setting)) {
            foreach ($def_setting as $section_key => $setting) {
                foreach ($setting as $field => $value) {
                    if (!empty($value['file_name'])) {
                        $value = [json_encode(uploadObMedia('demo-content/' . $value['file_name']))];
                    }
                    if (isset($value) && !is_null($value)) {
                        Settings::set($section_key, $field, $value);
                    }
                }
            }
            Cache::forget('optionbuilder__settings');
        }

        // Email templates
        $emailTemplates = [
            'inviteUser' => [
                'title' => __('forumwise::forum_wise.invite_user_title'),
                'roles' => [
                    'student' => [
                        'fields' => [
                            'info' => [
                                'title' => __('forumwise::forum_wise.variables_used'),
                                'icon' => 'icon-info',
                                'desc' => __('forumwise::forum_wise.invite_user_student_variables'),
                            ],
                            'subject' => [
                                'id' => 'subject',
                                'title' => __('forumwise::forum_wise.subject'),
                                'default' => __('forumwise::forum_wise.invite_user_subject'),
                            ],
                            'greeting' => [
                                'id' => 'greeting',
                                'title' => __('forumwise::forum_wise.greeting_text'),
                                'default' => __('forumwise::forum_wise.greeting', ['userName' => '{userName}']),
                            ],
                            'content' => [
                                'id' => 'content',
                                'title' => __('forumwise::forum_wise.email_content'),
                                'default' => __('forumwise::forum_wise.invite_user_content', ['userName' => '{userName}', 'forumTopicTitle' => '{forumTopicTitle}', 'inviteLink' => '{inviteLink}', 'message' => '{message}']),
                            ],
                        ],
                    ],
                    'tutor' => [
                        'fields' => [
                            'info' => [
                                'title' => __('forumwise::forum_wise.variables_used'),
                                'icon' => 'icon-info',
                                'desc' => __('forumwise::forum_wise.invite_user_tutor_variables'),
                            ],
                            'subject' => [
                                'id' => 'subject',
                                'title' => __('forumwise::forum_wise.subject'),
                                'default' => __('forumwise::forum_wise.invite_user_subject'),
                            ],
                            'greeting' => [
                                'id' => 'greeting',
                                'title' => __('forumwise::forum_wise.greeting_text'),
                                'default' => __('forumwise::forum_wise.greeting', ['userName' => '{userName}']),
                            ],
                            'content' => [
                                'id' => 'content',
                                'title' => __('forumwise::forum_wise.email_content'),
                                'default' => __('forumwise::forum_wise.invite_user_content', ['userName' => '{userName}', 'forumTopicTitle' => '{forumTopicTitle}', 'inviteLink' => '{inviteLink}', 'message' => '{message}']),
                            ],
                        ],
                    ],
                ],
            ],
        ];

        foreach ($emailTemplates as $type => $template) {
            foreach ($template['roles'] as $role => $data) {
                EmailTemplate::firstOrCreate(['type' => $type, 'role' => $role], [
                    'type' => $type,
                    'title' => $template['title'],
                    'role' => $role,
                    'content' => [
                        'info' => $data['fields']['info']['desc'],
                        'subject' => $data['fields']['subject']['default'],
                        'greeting' => $data['fields']['greeting']['default'],
                        'content' => $data['fields']['content']['default']
                    ]
                ]);
            }
        }
    }

    // Store image function
    // private function storeImage($imageFileName, $targetDirectory)
    // {
    //     $sourceImagePath = public_path($targetDirectory . $imageFileName);
    //     $defaultImagePath = public_path('modules/forumwise/images/' . 'placeholder.png');

    //     if (file_exists($sourceImagePath)) {
    //         $imageContent = file_get_contents($sourceImagePath);
    //     } else {
    //         if (file_exists($defaultImagePath)) {
    //             $imageContent = file_get_contents($defaultImagePath);
    //         } else {
    //             throw new \Exception("Default image not found at: $defaultImagePath");
    //         }
    //     }

    //     if (!File::exists(storage_path('app/public/' . $targetDirectory))) {
    //         File::makeDirectory(storage_path('app/public/' . $targetDirectory), 0755, true);
    //     }
       
    //     Storage::disk(getStorageDisk())->put('forumwise/images/' . $imageFileName, $imageContent);

    //     return $imageFileName;
    // }
}
