<?php 
namespace App\Repositories;

use App\Entities\Like;
use Illuminate\Http\Request;
use App\Repositories\Foundation\Repository;

class LikeRepository extends Repository
{
	protected $like;

	public function __construct(Like $like)
	{
		$this->like = new Repository($like);
	}

	public function getLikes(Request $request, $id)
	{
		return $this->like->getModel()->where('post_id', $id)->with(['user' => function($query) {
            $query->withCount(['followers as following' => function($query) {
                $query->where('follower_id', auth('api')->user()->id);
            }]);
        }])->paginate(6);
	}

	public function likePost(Request $request, $id)
	{
		$like = $this->like->getModel()
                           ->where('post_id', $id)
                    	   ->where('user_id', $request->user()->id)
                		   ->first();

        if ($like) {
            $like->delete();
        } else {
            $like = $this->like->getModel();
            $like->post_id = $id;
            $like->user_id = $request->user()->id;
            $like->save();
        }
	}
}