<?php 
namespace App\Repositories;

use App\Entities\Post;
use App\Entities\SavedPost;
use Illuminate\Http\Request;
use App\Repositories\UserRepository;
use App\Repositories\Foundation\Repository;

class PostRepository extends Repository
{
	protected $post;
	protected $userRepository;

	public function __construct(Post $post, SavedPost $savedPost, UserRepository $userRepository)
	{
		$this->post = new Repository($post);
		$this->savedPost = new Repository($savedPost);
		$this->userRepository = $userRepository;
	}

    public function getSinglePost(Request $request, $slug)
    {
        return $this->userRepository->relatedFields(
            $this->post->getModel()
        )->where('slug', $slug)->first();
    }

    public function getExplorePosts(Request $request)
    {
        return $this->userRepository->relatedFields(
            $this->post->getModel()
        )->orderBy('comments_count', 'desc')
        ->orderBy('likes_count', 'desc')
        ->createdAtDescending()->paginate(21);
    }

	public function createPost(Request $request)
	{
		$post = $this->post->getModel();
        $post->user_id = $request->user()->id;
        $post->slug = uniqid();
        $post->photo = str_random(3) . '-' . date('dmYhis') . '.' . $request->photo->extension();
        $post->caption = $request->caption;
        $post->save();

        $request->photo->storeAs('/media/' . $post->user_id . '/post-photos', $post->photo, 'local_public');

        return $this->userRepository->relatedFields(
        	$this->post->getModel()
        )->where('id', $post->id)->first();
	}

	public function savePost(Request $request, $id)
	{
		$savedPost = $this->savedPost->getModel()
                           			 ->where('post_id', $id)
                    	   			 ->where('user_id', $request->user()->id)
                		   			 ->first();

        if ($savedPost) {
            $savedPost->delete();
        } else {
            $savedPost = $this->savedPost->getModel();
            $savedPost->post_id = $id;
            $savedPost->user_id = $request->user()->id;
            $savedPost->save();
        }
	}

    public function deletePost(Request $request, $id)
    {
        $post = $this->post->show($id);
        \Storage::disk('local_public')->delete('/media/' . $post->user_id . '/post-photos', $post->photo);
        
        $this->post->delete($id);
    }
}