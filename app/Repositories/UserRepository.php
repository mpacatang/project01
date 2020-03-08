<?php 
namespace App\Repositories;

use App\Entities\User;
use App\Entities\Activation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Repositories\Foundation\Repository;

class UserRepository extends Repository
{
	protected $user;

	public function __construct(User $user, Activation $activation)
	{
		$this->user = new Repository($user);
		$this->activation = new Repository($activation);
	}

	public function getUserByUsername(Request $request)
    {
    	return $this->user->getModel()
						->where(filter_var(
							$request->username, FILTER_VALIDATE_EMAIL
						) ? 'email' : 'username', $request->username);
    }

    public function attemptLogin(Request $request)
    {
    	return Auth::once([
    		filter_var(
    			$request->username, FILTER_VALIDATE_EMAIL
    		) ? 'email' : 'username' => $request->username,
    		'password' => $request->password
    	]);
    }

	public function validateActiveUser(Request $request)
    {
    	if ($this->attemptLogin($request)) {
    		return $this->getUserByUsername($request)
						->whereHas('activations', function ($query) {
							$query->whereNotNull('completed_at');
						})->count();
    	} else {
    		return null;
    	}
    }

    public function activateAccount(Request $request)
    {
    	$activation = $this->activation->getModel()
    									->where('code', $request->activation_code)
    									->first();

    	$user = $this->user->getModel()
    						->where('id', $activation->user_id)
    						->whereHas('activations', function ($query) {
								$query->whereNotNull('completed_at');
							});

    	if ($activation->completed_at == null && !$user->count()) {
    		$activation->update([
    			'completed_at' => date('Y-m-d')
    		]);

    		return [
				'access_token' => $user->first()->createToken('token')->accessToken,
    			'token_type' => 'Bearer'
	    	];
    	} else {
    		return false;
    	}
    }

	public function getNotifications(Request $request)
    {
        $notifications = $request->user()
        						 ->notifications()
        						 ->with([
        						 	'commentNotification' => function($query) {
        						 		$query->with(['comment' => function($query) {
        						 			$query->with([
        						 				'post',
        						 				'user'
        						 			]);
        						 		}]);
        						 	},
        						 	'followNotification' => function($query) {
        						 		$query->with(['follow' => function($query) {
        						 			$query->with('followerUser');
        						 		}]);
        						 	},
        						 	'likeNotification' => function($query) {
        						 		$query->with(['like' => function($query) {
        						 			$query->with([
        						 				'post',
        						 				'user'
        						 			]);
        						 		}]);
        						 	}
        						 ])
        						 ->paginate(10);

		return $notifications;
    }

	public function getProfile(Request $request)
    {
        $user = $request->user()->load(['stories' => function($query) {
					$query->with(['storyViewers' => function($query) {
						$query->where('user_id', '=', auth('api')->user()->id);
					}]);
				}]);

        $lastViewedStory = $user->stories->map(function($story, $key) {
			if (count($story->storyViewers) > 0) {
				return $key;
			}
		})->reject(function ($story) {
			return $story === null;
		});

		$user->viewed_all_stories = count($lastViewedStory) && $lastViewedStory->last() + 1 == count($user->stories);
		$user->last_viewed_story_index = count($lastViewedStory) ? $lastViewedStory->last() : null;

		return $user;
    }

    public function updateProfile(Request $request)
	{
		$user = $request->user();
        $user->name = $request->name;
        $user->username = $request->username;
        $user->gender = $request->gender;
        $user->phone_number = $request->phone_number;
        $user->email = $request->email;
        $user->bio = $request->bio;
        $user->save();

        return $user;
	}

	public function changePassword(Request $request)
	{
		$user = $request->user();
		$user->password = bcrypt($request->new_password);
		$user->save();

        return $user;
	}

	public function resetPassword(Request $request)
	{
		return $this->user->getModel()
							->whereHas('passwordResets', function ($query) use ($request) {
								$query->where('code', $request->reset_code);
							})->update([
								'password' => bcrypt($request->new_password)
							]);
	}

	public function relatedFields($post)
	{
		return $post->with([
            'user' => function($query) {
                $query->withCount(['followers as following' => function($query) {
		            $query->where('follower_id', auth('api')->user()->id);
		        }]);
            }, 
            'comments' => function($query) {
                $query->with('user');
            }
        ])
        ->withCount([
            'comments',
            'likes', 
            'likes as liked' => function($query) {
                $query->where('user_id', auth('api')->user()->id);
            }, 
            'savedPosts as saved' => function($query) {
                $query->where('user_id', auth('api')->user()->id);
            }
        ]);
	}	

	public function getUserFeed(Request $request)
	{
		return $this->relatedFields(
			$request->user()->feed()
		)->createdAtDescending()->paginate(5);
	}

	public function getSavedPosts(Request $request)
	{
		return $this->relatedFields(
			$request->user()->savedPosts()
		)->createdAtDescending()->paginate(5);
	}

	public function getUserStories(Request $request)
	{
		$userStories = $request->user()
							   ->follows()
							   ->where('id', '!=', auth('api')->user()->id)
							   ->with(['followingUser' => function($query) {
									$query->with(['stories' => function($query) {
										$query->with(['storyViewers' => function($query) {
											$query->where('user_id', '=', auth('api')->user()->id);
										}]);
									}]);
							   }])
							   ->whereHas('followingUser', function($query) {
									$query->withCount(['stories'])
										  ->having('stories_count', '>', 0);
							   })
							   ->paginate(5);

		$userStories->map(function($userStory) {
			$lastViewedStory = $userStory->followingUser->stories->map(function($story, $key) {
				if (count($story->storyViewers) > 0) {
					return $key;
				}
			})->reject(function ($story) {
				return $story === null;
			});

			$userStory->followingUser->viewed_all_stories = count($lastViewedStory) && $lastViewedStory->last() + 1 == count($userStory->followingUser->stories);
			$userStory->followingUser->last_viewed_story_index = count($lastViewedStory) ? $lastViewedStory->last() : null;
			
			return $userStory;
		});

		return $userStories;
	}

	public function registerNewUser($request)
	{
		$user = $this->user->getModel();
        $user->name = $request['name'];
        $user->username = $request['username'];
        $user->email = $request['email'];
        $user->password = isset($request['password']) ? $request['password'] : '';
        $user->save();

        return $user;
	}

	public function validateUsername($name)
	{
		$username = strtolower(preg_replace('/[^\w]/', '', $name));
		$usernameExist = $this->user->getModel()->where('username', $username)->first();

		if ($usernameExist) {
			return $username . uniqid();
		} else {
			return $username;
		}
	}

	public function findOrCreateUser($user)
	{
		$userExist = $this->user->getModel()->where('email', $user->email)->first();

		if ($userExist) {
            return $userExist;
        } else {
        	return $this->registerNewUser([
        		'name' => $user->name,
				'email' => $user->email,
				'username' => $this->validateUsername($user->name)
        	]);
        }
	}

	public function getUserPosts(Request $request, $username)
	{
		return $this->relatedFields(
			$this->user->getModel()->where('username', $username)->first()->posts()
		)->createdAtDescending()->paginate(9);
	}

	public function getSingleUser(Request $request, $username)
	{
		$user = $this->user->getModel()
						   ->where('username', '=', $username)
						   ->with(['stories' => function($query) {
							   $query->with(['storyViewers' => function($query) {
							   		$query->where('user_id', '=', auth('api')->user()->id);
							   }]);
						   }])
						   ->withCount([
						   		'followers as following' => function($query) {
					            	$query->where('follower_id', auth('api')->user()->id);
					        	},
					        	'posts',
						   		'followers' => function($query) use ($username) {
						   			$query->whereHas('followerUser', function($query) use ($username) {
							   			$query->where('username', '!=', $username);
						   			});
						   		},
						   		'follows' => function($query) use ($username) {
						   			$query->whereHas('followingUser', function($query) use ($username) {
							   			$query->where('username', '!=', $username);
						   			});
						   		}
					       ])
						   ->first();


		$lastViewedStory = $user->stories->map(function($story, $key) {
			if (count($story->storyViewers) > 0) {
				return $key;
			}
		})->reject(function ($story) {
			return $story === null;
		});

		$user->viewed_all_stories = count($lastViewedStory) && $lastViewedStory->last() + 1 == count($user->stories);
		$user->last_viewed_story_index = count($lastViewedStory) ? $lastViewedStory->last() : null;

		return $user;
	}

	public function getUsers(Request $request)
	{
		return $this->user->getModel()
						  ->where('username', 'like', '%' . $request->username . '%')
                   		  ->where('id', '!=', $request->user()->id)
                   		  ->take(5)->get();
	}

	public function getSuggestedPeople(Request $request)
    {
        $users = $this->user->getModel()
							->whereNotIn('id', $request->user()->follows()->pluck('user_id')->all())
							->with(['stories' => function($query) {
								$query->with(['storyViewers' => function($query) {
									$query->where('user_id', '=', auth('api')->user()->id);
								}]);
							}])
							->withCount(['followers as following' => function($query) {
					            $query->where('follower_id', auth('api')->user()->id);
					        }])
        					->take(5)
        					->get();

        $users->map(function($user) {
			$lastViewedStory = $user->stories->map(function($story, $key) {
				if (count($story->storyViewers) > 0) {
					return $key;
				}
			})->reject(function ($story) {
				return $story === null;
			});

			$user->viewed_all_stories = count($lastViewedStory) && $lastViewedStory->last() + 1 == count($user->stories);
			$user->last_viewed_story_index = count($lastViewedStory) ? $lastViewedStory->last() : null;
			
			return $user;
		});

		return $users;
    }
}