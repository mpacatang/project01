<?php
namespace App\Http\Controllers\API\Main;

use Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\MessageRepository;

class MessageController extends Controller
{
    protected $messageRepository;

    public function __construct(MessageRepository $messageRepository)
    {
        $this->messageRepository = $messageRepository;
    }

    /**
     * Get List of Header Messages
     *
     * @access  public
     * @param   
     * @return  json(array)
     */

    public function getMessages(Request $request)
    {
        return response()->json(
            $this->messageRepository->getMessages($request), 200, [], JSON_NUMERIC_CHECK
        );
    }

    /**
     * Get List of Detail Messages
     *
     * @access  public
     * @param   
     * @return  json(array)
     */

    public function getMessageList(Request $request)
    {
        return response()->json(
            $this->messageRepository->getMessageList($request), 200, [], JSON_NUMERIC_CHECK
        );
    }

    /**
     * Post a New Message
     *
     * @access  public
     * @param   
     * @return  json(array)
     */

    public function postMessage(Request $request, $username)
    {
        $rules = [
            'message' => 'required'
        ];
        $validator = Validator::make($request->all(), $rules);
        $validator->validate();

        return response()->json(
            $this->messageRepository->postMessage($request, $username), 200, [], JSON_NUMERIC_CHECK
        );
    }

    /**
     * Mark Message as Read
     *
     * @access  public
     * @param   
     * @return  json(array)
     */

    public function readMessages(Request $request, $username)
    {
        return response()->json(
            $this->messageRepository->readMessages($request, $username), 200, [], JSON_NUMERIC_CHECK
        );
    }
}
