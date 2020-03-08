<?php
namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;
use Psr\Http\Message\ServerRequestInterface;
use Laravel\Passport\Http\Controllers\AccessTokenController;

class CustomAccessTokenController extends Controller
{
    protected $userRepository;
    protected $accessTokenController;

    public function __construct(AccessTokenController $accessTokenController, UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
        $this->accessTokenController = $accessTokenController;
    }

    /**
     * Hooks in before the AccessTokenController issues a token
     *
     *
     * @param  ServerRequestInterface $request
     * @return mixed
     */
    public function issueUserToken(ServerRequestInterface $request, Request $httpRequest)
    {
        if ($httpRequest->grant_type == 'password') {
            $validateActiveUser = $this->userRepository->validateActiveUser($httpRequest);

            if ($validateActiveUser !== null) {
                if ($validateActiveUser) {
                    return $this->accessTokenController->issueToken($request);
                } else {
                    return response()->json([
                        'error' => 'not_active_user',
                        'message' => 'Your account is not activated yet.'
                    ], 401);    
                }
            } else {
                return response()->json([
                    'error' => 'invalid_credentials',
                    'message' => 'The user credentials were incorrect.'
                ], 401);
            }
        }
    }
}