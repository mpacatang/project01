<?php 
namespace App\Repositories;

use App\Entities\Story;
use App\Entities\StoryViewer;
use Illuminate\Http\Request;
use App\Repositories\Foundation\Repository;

class StoryRepository extends Repository
{
	protected $storyRepository;
	protected $storyViewerRepository;

	public function __construct(Story $storyRepository, StoryViewer $storyViewerRepository)
	{
		$this->storyRepository = new Repository($storyRepository);
		$this->storyViewerRepository = new Repository($storyViewerRepository);
	}

	public function createStory(Request $request)
	{
		$storyRepository = $this->storyRepository->getModel();
        $storyRepository->user_id = $request->user()->id;
        $storyRepository->photo = str_random(3) . '-' . date('dmYhis') . '.' . $request->photo->extension();
        $storyRepository->caption = $request->caption;
        $storyRepository->save();

        $request->photo->storeAs('/media/' . $storyRepository->user_id . '/story-photos', $storyRepository->photo, 'local_public');

        return $storyRepository;
	}

	public function viewStory(Request $request, $id)
	{
		$this->storyViewerRepository->getModel()->firstOrCreate([
			'story_id' => $id,
			'user_id' => $request->user()->id
		]);
	}
}