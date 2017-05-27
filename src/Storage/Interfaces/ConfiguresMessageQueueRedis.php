<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPMQ\Server\Storage\Interfaces;

/**
 * Interface ConfiguresMessageQueueRedis
 * @package PHPMQ\Server\Storage\Interfaces
 */
interface ConfiguresMessageQueueRedis
{
	public function getHost() : string;

	public function getPort() : int;

	public function getDatabase() : int;

	public function getTimeout() : float;

	public function getPassword() : ?string;

	public function getPrefix() : ?string;

	public function getBackgroundSaveBehaviour() : int;
}
