<?php

namespace Wikibase\Search\ElasticSearch;

use Elastica\Client;

class ClientFactory {

	/**
	 * @var array
	 */
	private $hosts;

	/**
	 * @param array $hosts
	 */
	public function __construct( array $hosts ) {
		$this->hosts = $hosts;
	}

	public function newClient() {
		return new Client( [
			'servers' => $this->getServers()
		] );
	}

	private function getServers() {
		$servers = array();

		foreach ( $this->hosts as $host ) {
			$servers[] = [
				'host' => $host
			];
		}

		return $servers;
	}

}
