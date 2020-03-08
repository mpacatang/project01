<?php 
namespace App\Repositories;

use App\Entities\User;
use App\Entities\Message;
use App\Entities\MessageDetail;
use Illuminate\Http\Request;
use App\Repositories\Foundation\Repository;

class MessageRepository extends Repository
{
	protected $user;
	protected $message;
	protected $messageDetail;

	public function __construct(User $user, Message $message, MessageDetail $messageDetail)
	{
		$this->user = new Repository($user);
		$this->message = new Repository($message);
		$this->messageDetail = new Repository($messageDetail);
	}

	public function getMessages(Request $request)
	{
		$messages = $this->message->getModel()->where('sender_id', $request->user()->id)
										->orWhere('recipient_id', $request->user()->id)
										->with([
											'userSender', 
											'userRecipient',
											'lastMessageDetails'
										])
										->orderBy('id', 'desc')
										->paginate(6);

		$messages = $messages->map(function($message) use ($request) {
			return [
				'id' => $message->id,
				'user' => $this->getRecipientUser($request, $message),
				'unread' => $this->getUnreadMessages($request, $message),
				'last_message_details' => $message->lastMessageDetails
			];
		})->reduce(function ($messages, $message) {
			$messages[$message['user']->username] = $message;
			return $messages;
		}, []);

		return $messages;
	}

	public function getRecipientUser($request, $message)
	{
		if ($message->sender_id == $request->user()->id) {
			return $message->userRecipient;
		} else {
			return $message->userSender;
		}
	}

	public function getUnreadMessages($request, $message)
	{
		if ($message->lastMessageDetails->sender_id == $request->user()->id) {
			return 0;
		} else {
			return $this->messageDetail->getModel()
									->where('message_id', $message->id)
									->where('read', 0)
									->count();
		}
	}

	public function getMessageList(Request $request)
	{
		$messageDetails = $this->messageDetail->getModel()
											->with('user')
											->whereHas('message', function($query) use ($request) {
												$query->where(function($query) use ($request) {
													$query->whereHas('userRecipient', function($query) use ($request) {
														$query->where('username', $request->username);
													})
													->where('sender_id', $request->user()->id);
												})->orWhere(function($query) use ($request) {
													$query->whereHas('userSender', function($query) use ($request) {
														$query->where('username', $request->username);
													})
													->where('recipient_id', $request->user()->id);
												});
											})->orderBy('id', 'desc')->paginate(6)->getCollection();
		
		return $messageDetails->reverse()->values()->all();
	}

	public function postMessage(Request $request, $username)
	{
		$currentMessage = $this->message->getModel()
										->where(function($query) use ($request) {
											$query->whereHas('userRecipient', function($query) use ($request) {
												$query->where('username', $request->username);
											})
											->where('sender_id', $request->user()->id);
										})->orWhere(function($query) use ($request) {
											$query->whereHas('userSender', function($query) use ($request) {
												$query->where('username', $request->username);
											})
											->where('recipient_id', $request->user()->id);
										})->first();

		if (!$currentMessage) {
			$message = $this->message->getModel();
	        $message->sender_id = $request->user()->id;
	        $message->recipient_id = $this->user->getModel()->where('username', $username)->first()->id;
	        $message->save();

	        return $this->saveMessageDetail($request, $message)->load(['user']);
		} else {
			return $this->saveMessageDetail($request, $currentMessage)->load(['user']);
		}
	}

	public function saveMessageDetail($request, $message)
	{
		$messageDetail = $this->messageDetail->getModel();
		$messageDetail->read = 0;
        $messageDetail->message_id = $message->id;
        $messageDetail->sender_id = $request->user()->id;
        $messageDetail->message = $request->message;
        $messageDetail->save();

        return $messageDetail;
	}

	public function readMessages(Request $request, $username)
	{
		$messageDetail = $this->messageDetail->getModel()
											->whereHas('message', function($query) use ($request, $username) {
												$query->where(function($query) use ($request, $username) {
													$query->whereHas('userRecipient', function($query) use ($request, $username) {
														$query->where('username', $username);
													})
													->where('sender_id', $request->user()->id);
												})->orWhere(function($query) use ($request, $username) {
													$query->whereHas('userSender', function($query) use ($request, $username) {
														$query->where('username', $username);
													})
													->where('recipient_id', $request->user()->id);
												});
											})
											->where('sender_id', '!=', $request->user()->id)
											->where('read', 0);
        $messageDetail->update([
        	'read' => 1
        ]);
	}	
}