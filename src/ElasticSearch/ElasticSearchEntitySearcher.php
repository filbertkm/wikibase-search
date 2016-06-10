<?php

namespace Wikibase\Search\ElasticSearch;

use Elastica\Client;
use Elastica\Search;
use Wikibase\Datamodel\Entity\EntityIdParser;
use Wikibase\DataModel\Term\Term;
use Wikibase\Search\EntitySearcher;
use Wikibase\Search\TermSearchResult;

/**
 * @license GPL-2.0+
 * @author Katie Filbert < aude.wiki@gmail.com >
 */
class ElasticSearchEntitySearcher implements EntitySearcher {

	/**
	 * @var ClientFactory
	 */
	private $clientFactory;

	/**
	 * @var EntityIdParser
	 */
	private $entityIdParser;

	/**
	 * @param ClientFactory $clientFactory
	 * @param EntityIdParser $entityIdParser
	 */
	public function __construct( ClientFactory $clientFactory, EntityIdParser $entityIdParser ) {
		$this->clientFactory = $clientFactory;
		$this->entityIdParser = $entityIdParser;
	}

	/**
	 * @param string $text Term text to search for
	 * @param string $languageCode Language code to search in
	 * @param string $entityType Type of Entity to return
	 * @param string[] $termTypes Types of Term to return ('label', 'description', 'alias')
	 *
	 * @return TermSearchResult[]
	 */
	public function search( $text, $languageCode, $entityType, array $termTypes ) {
		$query = new \Elastica\Query\Match();
		$query->setField( 'label_en', [ 'query' => $text ] );

		$search = new Search( $this->clientFactory->newClient() );
		$search->setQuery( $query );

		$resultSet = $search->search();

		$termSearchResults = [];

		foreach ( $resultSet as $result ) {
			$termSearchResults[] = $this->getTermSearchResultforHit( $result->getHit() );
		}

		return $termSearchResults;
	}

	/**
	 * param array $hit
	 *
	 * @return TermSearchResult
	 */
	private function getTermSearchResultforHit( array $hit ) {
		$term = new Term( 'en', $hit['_source']['label_en'] );

		$termSearchResult = new TermSearchResult(
			$term,
			'label',
			$this->entityIdParser->parse( $hit['_id'] )
		);

		return $termSearchResult;
	}

}
