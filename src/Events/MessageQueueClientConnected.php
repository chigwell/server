<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPMQ\Server\Events;

use PHPMQ\Server\Clients\MessageQueueClient;
use PHPMQ\Server\Interfaces\CarriesEventData;

/**
 * Class MessageQueueClientConnected
 * @package PHPMQ\Server\Events
 */
final class MessageQueueClientConnected implements CarriesEventData
{
	/** @var MessageQueueClient */
	private $messageQueueClient;

	public function __construct( MessageQueueClient $messageQueueClient )
	{
		$this->messageQueueClient = $messageQueueClient;
	}

	public function getMessageQueueClient() : MessageQueueClient
	{
		return $this->messageQueueClient;
	}
}