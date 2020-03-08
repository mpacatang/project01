<?php 
namespace App\Repositories;

use App\Entities\Message;
use App\Entities\MessageDetail;
use App\Repositories\MessageRepository;
use Illuminate\Http\Request;
use App\Repositories\Foundation\Repository;

class RealtimeRepository extends Repository
{
	protected $message;
	protected $messageDetail;

	public function __construct(Message $message, MessageDetail $messageDetail, MessageRepository $messageRepository)
	{
		$this->message = new Repository($message);
		$this->messageDetail = new Repository($messageDetail);
		$this->messageRepository = $messageRepository;
	}

	public function getRealtime(Request $request)
	{
		$response = [];

		if ($request->has('messages')) {
			$response['messages'] = $this->getMessages($request);
		}

		if ($request->has('message_details')) {
			$response['message_details'] = $this->getMessageDetails($request);
		}

		if ($request->has('new_messages')) {
			$response['new_messages'] = $this->getNewMessages($request);
		}

		return $response;
	}

	public function getNewMessages($request)
	{
		$messages = $this->message->getModel()->where(function($query) use ($request) {
											$query->where('sender_id', $request->user()->id)
												->orWhere('recipient_id', $request->user()->id);
										})
										->where('id', '>', $request->new_messages['last_message_id'])
										->with([
											'userSender', 
											'userRecipient',
											'lastMessageDetails'
										])
										->orderBy('id', 'desc')
										->get();

		$messages = $messages->map(function($message) use ($request) {
			return [
				'id' => $message->id,
				'user' => $this->messageRepository->getRecipientUser($request, $message),
				'unread' => $this->messageRepository->getUnreadMessages($request, $message),
				'last_message_details' => $message->lastMessageDetails
			];
		})->reduce(function ($messages, $message) {
			$messages[$message['user']->username] = $message;
			return $messages;
		}, []);

		return $messages;
	}

	public function getMessageByUsername($request, $username)
	{
		return $this->message->getModel()
							->where(function($query) use ($request, $username) {
								$query->whereHas('userRecipient', function($query) use ($username) {
									$query->where('username', $username);
								})
								->where('sender_id', $request->user()->id);
							})->orWhere(function($query) use ($request, $username) {
								$query->whereHas('userSender', function($query) use ($username) {
									$query->where('username', $username);
								})
								->where('recipient_id', $request->user()->id);
							});
	}

	public function getMessages($request)
	{
		$messages = [];

		foreach ($request->messages as $key => $messageRequest) {
			$message = $this->getMessageByUsername($request, $messageRequest['username'])
							->with('lastMessageDetails')
							->first();

			$messages[] = [
				'user' => $this->messageRepository->getRecipientUser($request, $message),
				'unread' => $this->messageRepository->getUnreadMessages($request, $message),
				'last_message_details' => $message->lastMessageDetails
			];
		}

		return $messages;
	}

	public function getMessageDetails($request)
	{
		$message = $this->getMessageByUsername($request, $request->message_details['username'])
						->with('lastMessageDetails')
						->first();

		return [
			'user' => $this->messageRepository->getRecipientUser($request, $message),
			'last_read_message_id' => $this->getLastReadMessageId($request, $message),
			'messages' => $this->getLastMessageDetails($request, $message)
		];
	}

	public function getLastMessageDetails($request, $message)
	{
		$message->load(['messageDetails' => function($query) use ($request) {
			$query->where('id', '>', $request->message_details['last_message_details_id'])->with('user')->orderBy('id', 'desc');
		}]);

		return collect($message->messageDetails)->reverse()->values()->all();
	}

	public function getLastReadMessageId($request, $message)
	{
		if ($message->lastMessageDetails->sender_id != $request->user()->id) {
			return $message->lastMessageDetails->id + 1;
		} else {
			$lastUnreadMessage = $this->messageDetail->getModel()
									->where('message_id', $message->id)
									->where('read', 0)
									->first();

			if ($lastUnreadMessage) {
				return $lastUnreadMessage->id;
			} else {
				return $message->lastMessageDetails->id + 1;
			}
		}
	}
}