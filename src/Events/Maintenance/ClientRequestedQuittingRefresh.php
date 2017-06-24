<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPMQ\Server\Events\Maintenance;

use PHPMQ\Server\Clients\MaintenanceClient;
use PHPMQ\Server\Commands\QuitRefresh;
use PHPMQ\Server\Interfaces\CarriesEventData;

/**
 * Class ClientRequestedQuittingRefresh
 * @package PHPMQ\Server\Events\Maintenance
 */
final class ClientRequestedQuittingRefresh implements CarriesEventData
{
	/** @var MaintenanceClient */
	private $maintenanceClient;

	/** @var QuitRefresh */
	private $quitRefreshCommand;

	public function __construct( MaintenanceClient $maintenanceClient, QuitRefresh $quitRefreshCommand )
	{
		$this->maintenanceClient  = $maintenanceClient;
		$this->quitRefreshCommand = $quitRefreshCommand;
	}

	public function getMaintenanceClient() : MaintenanceClient
	{
		return $this->maintenanceClient;
	}

	public function getQuitRefreshCommand() : QuitRefresh
	{
		return $this->quitRefreshCommand;
	}
}