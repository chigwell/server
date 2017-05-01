<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace hollodotme\PHPMQ\Types;

use hollodotme\PHPMQ\Interfaces\IdentifiesQueue;
use hollodotme\PHPMQ\Storage\Interfaces\ProvidesQueueStatus;

/**
 * Class MessageQueueStatus
 * @package hollodotme\PHPMQ\Types
 */
final class MessageQueueStatus implements ProvidesQueueStatus
{
	/** @var array */
	private $statusData;

	public function __construct( array $statusData )
	{
		$this->statusData = $statusData;
	}

	public function getQueueName() : IdentifiesQueue
	{
		return new QueueName( (string)$this->statusData['queueName'] );
	}

	public function getCountTotal() : int
	{
		return (int)$this->statusData['countTotal'] ?? -1;
	}

	public function getCountUndispatched() : int
	{
		return (int)$this->statusData['countUndispatched'] ?? -1;
	}

	public function getCountDispatched() : int
	{
		return (int)$this->statusData['countDispatched'] ?? -1;
	}
}