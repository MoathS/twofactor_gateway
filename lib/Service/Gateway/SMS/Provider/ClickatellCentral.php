<?php

declare(strict_types=1);

/**
 * @author Christian Schrötter <cs@fnx.li>
 * @author Christoph Wurst <christoph@winzerhof-wurst.at>
 *
 * Nextcloud - Two-factor Gateway
 *
 * This code is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License, version 3,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License, version 3,
 * along with this program.  If not, see <http://www.gnu.org/licenses/>
 *
 */

namespace OCA\TwoFactorGateway\Service\Gateway\SMS\Provider;

use Exception;
use OCA\TwoFactorGateway\Exception\SmsTransmissionException;
use OCP\Http\Client\IClient;
use OCP\Http\Client\IClientService;

class ClickatellCentral implements IProvider {

	const PROVIDER_ID = 'clickatellcentral';

	/** @var IClient */
	private $client;

	/** @var ClickatellCentralConfig */
	private $config;

	public function __construct(IClientService $clientService,
								ClickatellCentralConfig $config) {
		$this->client = $clientService->newClient();
		$this->config = $config;
	}

	/**
	 * @param string $identifier
	 * @param string $message
	 *
	 * @throws SmsTransmissionException
	 */
	public function send(string $identifier, string $message) {
		$config = $this->getConfig();
		try {
			$this->client->get(vsprintf('https://api.clickatell.com/http/sendmsg?user=%s&password=%s&api_id=%u&to=%s&text=%s', [
				urlencode($config->getUser()),
				urlencode($config->getPassword()),
				$config->getApi(),
				urlencode($identifier),
				urlencode($message),
			]));
		} catch (Exception $ex) {
			throw new SmsTransmissionException();
		}
	}

	/**
	 * @return ClickatellCentralConfig
	 */
	public function getConfig(): IProviderConfig {
		return $this->config;
	}
}
