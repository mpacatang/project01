<?php
namespace App\Http\Controllers\API\Main;

use Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\PostRepository;
use App\Repositories\UserRepository;
use App\Repositories\LikeRepository;
use App\Repositories\CommentRepository;

class PostController extends Controller
{
    protected $postRepository;
    protected $userRepository;
    protected $likeRepository;
    protected $commentRepository;

    public function __construct(PostRepository $postRepository, UserRepository $userRepository, LikeRepository $likeRepository, CommentRepository $commentRepository)
    {
        $this->postRepository = $postRepository;
        $this->userRepository = $userRepository;
        $this->likeRepository = $likeRepository;
        $this->commentRepository = $commentRepository;
    }

    /**
     * Create a New Post
     *
     * @access  public
     * @param   
     * @return  json(array)
     */

    public function createPost(Request $request)
    {
        $rules = [
            'photo' => 'required|dimensions:max_width=5000,max_height=5000'
        ];
        $validator = Validator::make($request->all(), $rules);
        $validator->validate();

        return response()->json(
            $this->postRepository->createPost($request), 200, [], JSON_NUMERIC_CHECK
        );
    }

    /**
     * Get Single Post
     *
     * @access  public
     * @param   
     * @return  json(array)
     */

    public function getSinglePost(Request $request, $slug)
    {
        return response()->json(
            $this->postRepository->getSinglePost($request, $slug), 200, [], JSON_NUMERIC_CHECK
        );
    }

    /**
     * Get List of Explore Posts
     *
     * @access  public
     * @param   
     * @return  json(array)
     */

    public function getExplorePosts(Request $request)
    {
        return response()->json(
            $this->postRepository->getExplorePosts($request), 200, [], JSON_NUMERIC_CHECK
        );
    }

    /**
     * get List of User Feeds
     *
     * @access  public
     * @param   
     * @return  json(array)
     */

	public function getPosts(Request $request)
	{
        return response()->json(
            $this->userRepository->getUserFeed($request), 200, [], JSON_NUMERIC_CHECK
        );
	}

    /**
     * Get List of User Saved Posts
     *
     * @access  public
     * @param   
     * @return  json(array)
     */

    public function getSavedPosts(Request $request)
    {
        return response()->json(
            $this->userRepository->getSavedPosts($request), 200, [], JSON_NUMERIC_CHECK
        );
    }

    /**
     * Get List of User Posts
     *
     * @access  public
     * @param   
     * @return  json(array)
     */

    public function getUserPosts(Request $request, $username)
    {
        return response()->json(
            $this->userRepository->getUserPosts($request, $username), 200, [], JSON_NUMERIC_CHECK
        );
    }

    /**
     * Get List of Post Likes
     *
     * @access  public
     * @param   
     * @return  json(array)
     */

    public function getLikes(Request $request, $id)
    {
        return response()->json(
            $this->likeRepository->getLikes($request, $id), 200, [], JSON_NUMERIC_CHECK
        );
    }

    /**
     * Post a New Comment
     *
     * @access  public
     * @param   
     * @return  json(array)
     */

	public function postComment(Request $request, $id)
	{
		$rules = [
            'comment' => 'required'
        ];
        $validator = Validator::make($request->all(), $rules);
        $validator->validate();

        return response()->json(
            $this->commentRepository->postComment($request, $id), 200, [], JSON_NUMERIC_CHECK
        );
	}

    /**
     * Like a Post
     *
     * @access  public
     * @param   
     * @return  json(array)
     */

    public function likePost(Request $request, $id)
    {
        return response()->json(
            $this->likeRepository->likePost($request, $id), 200, [], JSON_NUMERIC_CHECK
        );
    }

    /**
     * Save a New Post
     *
     * @access  public
     * @param   
     * @return  json(array)
     */

    public function savePost(Request $request, $id)
    {
        return response()->json(
            $this->postRepository->savePost($request, $id), 200, [], JSON_NUMERIC_CHECK
        );
    }

    /**
     * Delete Post
     *
     * @access  public
     * @param   
     * @return  json(array)
     */

    public function deletePost(Request $request, $id)
    {
        return response()->json(
            $this->postRepository->deletePost($request, $id), 200, [], JSON_NUMERIC_CHECK
        );
    }
}
