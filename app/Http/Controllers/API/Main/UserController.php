<?php
namespace App\Http\Controllers\API\Main;

use Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;
use App\Repositories\FollowRepository;

class UserController extends Controller
{
    protected $userRepository;
    protected $followRepository;

    public function __construct(UserRepository $userRepository, FollowRepository $followRepository)
    {
        $this->userRepository = $userRepository;
        $this->followRepository = $followRepository;
    }

    /**
     * Get List of Notifications
     *
     * @access  public
     * @param   
     * @return  json(array)
     */

    public function getNotifications(Request $request)
    {
        return response()->json(
            $this->userRepository->getNotifications($request), 200, [], JSON_NUMERIC_CHECK
        );
    }

    /**
     * Get User Profile
     *
     * @access  public
     * @param   
     * @return  json(array)
     */

    public function getProfile(Request $request)
    {
        return response()->json(
            $this->userRepository->getProfile($request), 200, [], JSON_NUMERIC_CHECK
        );
    }

    /**
     * Get User Single Profile
     *
     * @access  public
     * @param   
     * @return  json(array)
     */

    public function getSingleUser(Request $request, $username)
    {
        return response()->json(
            $this->userRepository->getSingleUser($request, $username), 200, [], JSON_NUMERIC_CHECK
        );
    }

    /**
     * Get List of Filtered Users
     *
     * @access  public
     * @param   
     * @return  json(array)
     */

    public function getUsers(Request $request)
    {
    	$rules = [
			'username' => 'required'
		];
		$validator = Validator::make($request->all(), $rules);
		$validator->validate();

        return response()->json(
            $this->userRepository->getUsers($request), 200, [], JSON_NUMERIC_CHECK
        );
    }

    /**
     * Follow User
     *
     * @access  public
     * @param   
     * @return  json(array)
     */

    public function followUser(Request $request, $id)
    {
        return response()->json(
            $this->followRepository->followUser($request, $id), 200, [], JSON_NUMERIC_CHECK
        );
    }

    /**
     * Get List of Followers
     *
     * @access  public
     * @param   
     * @return  json(array)
     */

    public function getFollowers(Request $request, $id)
    {
        return response()->json(
            $this->followRepository->getFollowers($request, $id), 200, [], JSON_NUMERIC_CHECK
        );
    }

    /**
     * Get List of Followings
     *
     * @access  public
     * @param   
     * @return  json(array)
     */

    public function getFollowing(Request $request, $id)
    {
        return response()->json(
            $this->followRepository->getFollowing($request, $id), 200, [], JSON_NUMERIC_CHECK
        );
    }

    /**
     * get List of Suggested People
     *
     * @access  public
     * @param   
     * @return  json(array)
     */

    public function getSuggestedPeople(Request $request)
    {
        return response()->json(
            $this->userRepository->getSuggestedPeople($request), 200, [], JSON_NUMERIC_CHECK
        );
    }

    /**
     * Update User Profile
     *
     * @access  public
     * @param   
     * @return  json(array)
     */

    public function updateProfile(Request $request)
    {
        $rules = [
            'name' => 'required',
            'username' => 'required|unique:users,username,' . $request->user()->id,
            'gender' => 'required',
            'phone_number' => 'required',
            'email' => 'required|unique:users,email,' . $request->user()->id,
            'bio' => 'required'
        ];
        $validator = Validator::make($request->all(), $rules);
        $validator->validate();

        return response()->json(
            $this->userRepository->updateProfile($request), 200, [], JSON_NUMERIC_CHECK
        );
    }
}
