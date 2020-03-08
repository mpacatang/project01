<?php 
namespace App\Repositories;

use App\Entities\Follow;
use Illuminate\Http\Request;
use App\Repositories\Foundation\Repository;

class FollowRepository extends Repository
{
	protected $follow;

	public function __construct(Follow $follow)
	{
		$this->follow = new Repository($follow);
	}

	public function followUser(Request $request, $id)
	{
		$follow = $this->follow->getModel()
                               ->where('user_id', $id)
                        	   ->where('follower_id', $request->user()->id)
                        	   ->first();

        if ($follow) {
            $follow->delete();
        } else {
            $follow = $this->follow->getModel();
            $follow->user_id = $id;
            $follow->follower_id = $request->user()->id;
            $follow->save();
        }
	}

    public function getFollowers(Request $request, $id)
    {
        return $this->follow->getModel()
                            ->where('user_id', '=', $id)
                            ->where('follower_id', '!=', $id)
                            ->with(['followerUser' => function($query) {
                                $query->withCount(['followers as following' => function($query) {
                                    $query->where('follower_id', auth('api')->user()->id);
                                }]);
                            }])
                            ->paginate(6);
    }

    public function getFollowing(Request $request, $id)
    {
        return $this->follow->getModel()
                            ->where('follower_id', '=', $id)
                            ->where('user_id', '!=', $id)
                            ->with(['followingUser' => function($query) {
                                $query->withCount(['followers as following' => function($query) {
                                    $query->where('follower_id', auth('api')->user()->id);
                                }]);
                            }])
                            ->paginate(6);
    }
}