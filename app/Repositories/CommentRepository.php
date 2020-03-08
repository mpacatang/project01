<?php 
namespace App\Repositories;

use App\Entities\Comment;
use Illuminate\Http\Request;
use App\Repositories\Foundation\Repository;

class CommentRepository extends Repository
{
	protected $comment;

	public function __construct(Comment $comment)
	{
		$this->comment = new Repository($comment);
	}

	public function postComment(Request $request, $id)
	{
		$comment = $this->comment->getModel();
        $comment->post_id = $id;
        $comment->user_id = $request->user()->id;
        $comment->comment = $request->comment;
        $comment->save();

        return $comment;
	}
}