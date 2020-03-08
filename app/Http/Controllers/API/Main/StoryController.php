<?php
namespace App\Http\Controllers\API\Main;

use Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\StoryRepository;
use App\Repositories\UserRepository;

class StoryController extends Controller
{
    protected $storyRepository;
    protected $userRepository;

    public function __construct(StoryRepository $storyRepository, UserRepository $userRepository)
    {
        $this->storyRepository = $storyRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * Create a New Story
     *
     * @access  public
     * @param   
     * @return  json(array)
     */

    public function createStory(Request $request)
    {
        $rules = [
            'photo' => 'required|dimensions:max_width=5000,max_height=5000'
        ];
        $validator = Validator::make($request->all(), $rules);
        $validator->validate();

        return response()->json(
            $this->storyRepository->createStory($request), 200, [], JSON_NUMERIC_CHECK
        );
    }

    /**
     * Get List of Stories
     *
     * @access  public
     * @param   
     * @return  json(array)
     */

	public function getStories(Request $request)
	{
        return response()->json(
            $this->userRepository->getUserStories($request), 200, [], JSON_NUMERIC_CHECK
        );
	}

    /**
     * Mark The Story as Seen
     *
     * @access  public
     * @param   
     * @return  json(array)
     */

    public function viewStory(Request $request, $id)
    {
        return response()->json(
            $this->storyRepository->viewStory($request, $id), 200, [], JSON_NUMERIC_CHECK
        );
    }
}
