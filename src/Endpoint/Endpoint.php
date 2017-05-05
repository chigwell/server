<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace hollodotme\PHPMQ\Endpoint;

use hollodotme\PHPMQ\Clients\Client;
use hollodotme\PHPMQ\Clients\Interfaces\IdentifiesClient;
use hollodotme\PHPMQ\Clients\Types\ClientId;
use hollodotme\PHPMQ\Endpoint\Constants\SocketShutdownMode;
use hollodotme\PHPMQ\Endpoint\Interfaces\AcceptsMessageHandlers;
use hollodotme\PHPMQ\Endpoint\Interfaces\ConfiguresEndpoint;
use hollodotme\PHPMQ\Endpoint\Interfaces\ConsumesMessages;
use hollodotme\PHPMQ\Endpoint\Interfaces\DispatchesMessages;
use hollodotme\PHPMQ\Endpoint\Interfaces\HandlesMessage;
use hollodotme\PHPMQ\Endpoint\Interfaces\ListensToClients;
use hollodotme\PHPMQ\Protocol\Interfaces\BuildsMessages;
use hollodotme\PHPMQ\Protocol\Interfaces\CarriesInformation;
use hollodotme\PHPMQ\Protocol\Messages\MessageBuilder;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

/**
 * Class Endpoint
 * @package hollodotme\PHPMQ\Endpoint
 */
final class Endpoint implements ListensToClients, AcceptsMessageHandlers, LoggerAwareInterface
{
	use LoggerAwareTrait;

	/** @var ConfiguresEndpoint */
	private $config;

	/** @var resource */
	private $socket;

	/** @var array|Client[] */
	private $clients;

	/** @var bool */
	private $listening;

	/** @var BuildsMessages */
	private $messageBuilder;

	/** @var array|HandlesMessage[] */
	private $messageHandlers;

	/** @var DispatchesMessages */
	private $messageDispatcher;

	public function __construct( ConfiguresEndpoint $config, DispatchesMessages $messageDispatcher )
	{
		$this->config            = $config;
		$this->clients           = [];
		$this->listening         = false;
		$this->messageBuilder    = new MessageBuilder();
		$this->messageHandlers   = [];
		$this->messageDispatcher = $messageDispatcher;
	}

	public function addMessageHandlers( HandlesMessage ...$messageHandlers ) : void
	{
		foreach ( $messageHandlers as $messageHandler )
		{
			$messageHandler->setLogger( $this->logger );

			$this->messageHandlers[] = $messageHandler;
		}
	}

	public function startListening() : void
	{
		$this->establishSocket();

		$this->listening = true;

		$this->logger->debug( 'Start listening for client connections...' );

		while ( $this->listening )
		{
			$this->checkForNewClient();

			foreach ( $this->getActiveClients() as $client )
			{
				if ( $client->isDisconnected() )
				{
					$this->logger->debug( 'Client disconnected: ' . $client->getClientId() );

					$this->removeClient( $client->getClientId() );

					continue;
				}

				$message = $client->readMessage();

				if ( null === $message )
				{
					continue;
				}

				$this->handleMessageFromClient( $client, $message );
			}

			foreach ( $this->clients as $client )
			{
				$this->messageDispatcher->dispatchMessages( $client );
			}
		}
	}

	private function establishSocket() : void
	{
		if ( null !== $this->socket )
		{
			return;
		}

		$this->socket = socket_create(
			$this->config->getSocketDomain(),
			$this->config->getSocketType(),
			$this->config->getSocketProtocol()
		);

		if ( file_exists( $this->config->getBindToAddress()->getAddress() ) )
		{
			@unlink( $this->config->getBindToAddress()->getAddress() );
		}

		socket_bind(
			$this->socket,
			$this->config->getBindToAddress()->getAddress(),
			$this->config->getBindToAddress()->getPort()
		);

		socket_listen( $this->socket, $this->config->getListenBacklog() );
		socket_set_nonblock( $this->socket );
	}

	private function checkForNewClient() : void
	{
		$clientSocket = socket_accept( $this->socket );

		if ( $clientSocket !== false )
		{
			socket_set_nonblock( $clientSocket );

			$clientId = ClientId::generate();
			$client   = new Client( $clientId, $clientSocket, $this->messageBuilder );

			$this->logger->debug( 'New client connected: ' . $clientId );

			$this->clients[ $clientId->toString() ] = $client;
		}
	}

	/**
	 * @return array|Client[]
	 */
	private function getActiveClients() : array
	{
		if ( empty( $this->clients ) )
		{
			return [];
		}

		$reads  = [];
		$writes = $exepts = null;

		foreach ( $this->clients as $client )
		{
			$client->collectSocket( $reads );
		}

		socket_select( $reads, $writes, $exepts, 0 );

		return array_intersect_key( $this->clients, $reads );
	}

	private function removeClient( IdentifiesClient $clientId ) : void
	{
		unset( $this->clients[ $clientId->toString() ] );
	}

	private function handleMessageFromClient( ConsumesMessages $client, CarriesInformation $message ) : void
	{
		foreach ( $this->messageHandlers as $messageHandler )
		{
			if ( $messageHandler->acceptsMessageType( $message->getMessageType() ) )
			{
				$messageHandler->handle( $message, $client );
			}
		}
	}

	public function endListening() : void
	{
		$this->listening = false;

		if ( null !== $this->socket )
		{
			socket_shutdown( $this->socket, SocketShutdownMode::READING_WRITING );
			socket_close( $this->socket );
			$this->socket = null;
		}
	}

	public function __destruct()
	{
		$this->endListening();
	}
}
