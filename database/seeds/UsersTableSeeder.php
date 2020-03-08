<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	factory(App\Entities\User::class, 10)->create()->each(function ($user) {

            // Reset directory
            \Storage::disk('local_public')->deleteDirectory('/media/' . $user->id);

            // Profile photo
            \Storage::disk('local_public')->copy('/media-example/profile-' . rand(1, 3) . '.jpg', '/media/' . $user->id . '/profile-photo/' . $user->photo);

            // Posts
            $user->posts()->saveMany(
                factory(App\Entities\Post::class, rand(5, 20))->create([
                    'user_id' => $user->id
                ])->each(function($post) use ($user) {

                    // Post photo
                    \Storage::disk('local_public')->copy('/media-example/preview-' . rand(1, 9) . '.jpg', '/media/' . $user->id . '/post-photos/' . $post->photo);

                    // Comments
                    factory(App\Entities\Comment::class, rand(10, 30))->make([
                        'post_id' => $post->id
                    ])->each(function($comment) use ($post) {
                        $comment->user_id = rand(1, 10);
                        $savedComment = $post->comments()->save($comment);

                        // Notification
                        if ($comment->user_id != $post->user_id) {
                            $notification = \App\Entities\Notification::create([
                                'user_id' => $post->user_id,
                                'read_status' => '0',
                                'type' => 'comment'
                            ]);

                            // Comment Notification
                            $notification->commentNotification()->save(new App\Entities\CommentNotification([
                                'comment_id' => $savedComment->id
                            ]));
                        }
                    });

                    // Likes
                    $likers = range(1, rand(1, 10));
                    shuffle($likers);
                    for ($i = 0; $i < count($likers); $i++) {
                        $savedLike = $post->likes()->save(new App\Entities\Like([
                            'post_id' => $post->id,
                            'user_id' => $likers[$i]
                        ]));

                        // Notification
                        if ($savedLike->user_id != $post->user_id) {
                            $notification = \App\Entities\Notification::create([
                                'user_id' => $post->user_id,
                                'read_status' => '0',
                                'type' => 'like'
                            ]);

                            // Like Notification
                            $notification->likeNotification()->save(new App\Entities\LikeNotification([
                                'like_id' => $savedLike->id
                            ]));
                        }
                    }
                })
            );

            // Stories
            $user->stories()->saveMany(
                factory(App\Entities\Story::class, rand(1, 10))->create([
                    'user_id' => $user->id
                ])->each(function($story) use ($user) {

                    // Story photo
                    \Storage::disk('local_public')->copy('/media-example/preview-' . rand(1, 9) . '.jpg', '/media/' . $user->id . '/story-photos/' . $story->photo);
                })
            );

            // Followers
            for ($i = 1; $i <= 10; $i++) {
                $savedFollow = $user->followers()->save(new App\Entities\Follow([
                    'user_id' => $user->id,
                    'follower_id' => $i
                ]));

                // Notification
                if ($savedFollow->user_id != $savedFollow->follower_id) {
                    $notification = \App\Entities\Notification::create([
                        'user_id' => $savedFollow->user_id,
                        'read_status' => '0',
                        'type' => 'follow'
                    ]);

                    // Follow Notification
                    $notification->followNotification()->save(new App\Entities\FollowNotification([
                        'follow_id' => $savedFollow->id
                    ]));
                }
            }

            // Messages
            for ($i = 1; $i <= 10; $i++) {
                if ($user->id < $i) {
                    $message = $user->messagesAsSender()->save(new App\Entities\Message([
                        'sender_id' => $user->id,
                        'recipient_id' => $i
                    ]));

                    factory(App\Entities\MessageDetail::class, rand(10, 30))->make()->each(function($messageDetail) use ($user, $message, $i) {
                        $messageDetail->message_id = $message->id;
                        $messageDetail->sender_id = [$user->id, $i][rand(0, 1)];
                        $message->messageDetails()->save($messageDetail);  
                    });
                }
            }

            // Activation
	        $user->activations()->save(factory(App\Entities\Activation::class)->make());
	    });
    }
}
