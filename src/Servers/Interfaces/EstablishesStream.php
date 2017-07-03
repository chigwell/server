<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPMQ\Server\Servers\Interfaces;

/**
 * Class ServerSocket
 * @package PHPMQ\Server\Endpoint\Sockets
 */
interface EstablishesStream
{
	public function startListening() : void;

	public function getStream();
}