<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPMQ\Server\Endpoint\Interfaces;

/**
 * Interface TracksStreams
 * @package PHPMQ\Server\Endpoint\Interfaces
 */
interface TracksStreams
{
	public function addReadStream( $stream, callable $listener ) : void;

	public function addWriteStream( $stream, callable $listener ) : void;

	public function removeAllStreams() : void;

	public function removeStream( $stream ) : void;

	public function start() : void;

	public function stop() : void;
}
