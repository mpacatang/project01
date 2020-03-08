<?php
namespace App\Http\Controllers\API\Main;

use Hash;
use Validator;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
	protected $userRepository;

	public function __construct(UserRepository $userRepository)
	{
		$this->userRepository = $userRepository;
	}

	/**
     * Validate and Register User
     *
     * @access 	public
     * @param 	
     * @return 	json(array)
     */

	public function register(Request $request)
	{
		$rules = [
			'name' => 'required',
			'username' => 'required|unique:users,username',
			'email' => 'required|unique:users,email',
			'password' => 'required|confirmed|min:6'
		];
		$validator = Validator::make($request->all(), $rules);
		$validator->validate();

		$user = $this->userRepository->registerNewUser([
			'name' => $request->name,
			'username' => $request->username,
			'email' => $request->email,
			'password' => bcrypt($request->password)
		]);

		$user->sendNewUserActivation($user->activations()->create([
			'code' => str_random(30)
		])->code);
	}

	/**
     * Validate and Resend User Activation
     *
     * @access 	public
     * @param 	
     * @return 	json(array)
     */

	public function resendActivation(Request $request)
	{
		$rules = [
			'username' => 'required',
			'password' => 'required'
		];
		$validator = Validator::make($request->all(), $rules);
		$validator->validate();

		if ($this->userRepository->attemptLogin($request)) {
			$user = $this->userRepository->getUserByUsername($request)->first();
			$user->sendNewUserActivation($user->activations()->create([
				'code' => str_random(30)
			])->code);

			return response()->json(
				$user, 200, [], JSON_NUMERIC_CHECK
			);
		} else {
            return response()->json([
                'error' => 'invalid_credentials',
                'message' => 'The user credentials were incorrect.'
            ], 401);
		}
	}

	/**
     * Validate and Activate User Account
     *
     * @access 	public
     * @param 	
     * @return 	json(array)
     */

	public function activateAccount(Request $request)
	{
		$rules = [
			'activation_code' => 'required'
		];
		$validator = Validator::make($request->all(), $rules);
		$validator->validate();

		$activateAccount = $this->userRepository->activateAccount($request);

		if ($activateAccount) {
			return response()->json(
				$activateAccount, 200, [], JSON_NUMERIC_CHECK
			);
		} else {
            return response()->json([
                'error' => 'invalid_activation_link',
                'message' => 'The activation link is expired or have already been used.'
            ], 401);
		}
	}

	/**
     * Validate and Send Forgot Password Request
     *
     * @access 	public
     * @param 	
     * @return 	json(array)
     */

	public function requestForgotPassword(Request $request)
	{
		$rules = [
			'username' => 'required'
		];
		$validator = Validator::make($request->all(), $rules);
		$validator->validate();

		$user = $this->userRepository->getUserByUsername($request);

		if ($user->count()) {
			$user = $user->first();
			$user->sendUserForgotPassword($user->passwordResets()->create([
				'code' => str_random(30)
			])->code);

			return response()->json(
				$user, 200, [], JSON_NUMERIC_CHECK
			);
		} else {
            return response()->json([
                'error' => 'invalid_username',
                'message' => 'The email or username were incorrect.'
            ], 401);
		}
	}

	/**
     * Validate and Change Password
     *
     * @access 	public
     * @param 	
     * @return 	json(array)
     */

	public function changePassword(Request $request)
	{
		$user = $request->user();
		$rules = [
			'old_password' => 'required',
			'new_password' => 'required|confirmed|min:6'
		];

		$validator = Validator::make($request->all(), $rules);
		$validator->after(function($validator) use ($request, $user) {
			if (!Hash::check($request->old_password, $user->password)) {
				$validator->errors()->add('old_password', 'Wrong old password.');
			}
		});
		$validator->validate();

		$this->userRepository->changePassword($request);
	}

	/**
     * Validate and Reset Password
     *
     * @access 	public
     * @param 	
     * @return 	json(array)
     */

	public function resetPassword(Request $request)
	{
		$rules = [
			'reset_code' => 'required|exists:password_resets,code',
			'new_password' => 'required|confirmed|min:6'
		];
		$validator = Validator::make($request->all(), $rules);
		$validator->validate();

		$this->userRepository->resetPassword($request);
	}
}
