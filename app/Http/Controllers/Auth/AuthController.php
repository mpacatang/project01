<?php
namespace App\Http\Controllers\Auth;

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

	public function redirectToProvider($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    public function handleProviderCallback($provider)
    {
    	try {
    		$user = Socialite::driver($provider)->user();
    		$token = $this->userRepository->findOrCreateUser($user)->createToken('token')->accessToken;

    		session([
    			'token' => [
    				'access_token' => $token,
	    			'token_type' => 'Bearer'
    			]
	    	]);
    	}
    	finally {
			return view('main');
    	}
    }

    public function getAuthenticatedUser()
    {
    	$token = session('token');
    	session()->forget('token');
        return response()->json($token);
    }
}
