<?php

namespace Wikibase\Search\Tests\Integration\ElasticSearch;

use Elastica\Client;
use Elastica\Index;
use Elastica\Type;
use Wikibase\DataModel\Entity\BasicEntityIdParser;
use Wikibase\DataModel\Entity\EntityDocument;
use Wikibase\DataModel\Entity\Item;
use Wikibase\DataModel\Entity\ItemId;
use Wikibase\DataModel\Term\AliasGroup;
use Wikibase\DataModel\Term\Term;
use Wikibase\Search\ElasticSearch\ClientFactory;
use Wikibase\Search\ElasticSearch\ElasticSearchEntitySearcher;
use Wikibase\Search\ElasticSearch\Index\EntityIndexer;
use Wikibase\Search\ElasticSearch\Mapping\MappingCreator;
use Wikibase\Search\ElasticSearch\Mapping\MappingPropertiesBuilder;
use Wikibase\Search\TermSearchResult;

/**
 * @covers Wikibase\Search\ElasticSearch\ElasticSearchEntitySearcher
 *
 * @licence GNU GPL v2+
 * @author Katie Filbert < aude.wiki@gmail.com >
 */
class ElasticSearchEntitySearcherTest extends \PHPUnit_Framework_TestCase {

	protected function setUp() {
		parent::setUp();

		$this->getTestIndex()->create( [], [ 'recreate' => true ] );

		$this->initMapping();
		$this->loadTestEntities();
	}

	protected function tearDown() {
	//	$this->getTestIndex()->delete();
	}

	public function testTypeHasDocument() {
		foreach ( $this->getEntities() as $entity ) {
			$document = $this->getType()->getDocument( $entity->getId()->getSerialization() );
			$this->assertInstanceOf( 'Elastica\Document', $document );
		}
	}

	/**
	 * @dataProvider searchProvider
	 */
	public function testSearch( $expected, $search, $langCode, $entityType, array $termTypes ) {
		$clientFactory = new ClientFactory( [ '127.0.0.1' ] );
		$entitySearcher = new ElasticSearchEntitySearcher(
			new ClientFactory( [ '127.0.0.1' ] ),
			new BasicEntityIdParser()
		);

		$result = $entitySearcher->search( $search, $langCode, $entityType, $termTypes );

		$this->assertEquals( $expected, $result );
	}

	public function searchProvider() {
		$expected = [
			new TermSearchResult(
				new Term( 'en', 'kitten' ),
				'label',
				new ItemId( 'Q147' )
			)
		];

		return [
			[ $expected, 'kitten', 'en', 'item', [] ],
			[ $expected, 'Kitten', 'en', 'item', [] ]
		];
	}

	private function initMapping() {
		$mappingPropertiesBuilder = new MappingPropertiesBuilder( [ 'es' ] );
		$mappingCreator = new MappingCreator( $mappingPropertiesBuilder );

		$mapping = $mappingCreator->createMapping( $this->getType() );
		$mapping->send();
	}

	private function loadTestEntities() {
		$entityIndexer = new EntityIndexer( $this->getType() );

		foreach ( $this->getEntities() as $entity ) {
			$entityIndexer->index( $entity );
		}
	}

	/**
	 * @return EntityDocument
	 */
	private function getEntities() {
		$catItem = new Item( new ItemId( 'Q146' ) );
		$catItem->getFingerprint()->setLabel( 'en', 'cat' );

		$kittenItem = new Item( new ItemId( 'Q147' ) );

		$kittenItem->getFingerprint()->setLabel( 'en', 'kitten' );
		$kittenItem->getFingerprint()->setDescription( 'en', 'young cat' );
		$kittenItem->getFingerprint()->setAliasGroup( 'en', [ 'kitty' ] );

		$kittenItem2 = new Item( new ItemId( 'Q148' ) );
		$kittenItem2->getFingerprint()->setLabel( 'en', 'Kitten' );

		return [ $catItem, $kittenItem, $kittenItem2 ];
	}

	/**
	 * @return Type
	 */
	private function getType() {
		return new Type( $this->getTestIndex(), 'entities' );
	}

	/**
	 * @return Index
	 */
	private function getTestIndex() {
		$clientFactory = new ClientFactory( [ '127.0.0.1' ] );
		$client = $clientFactory->newClient();

		return new Index( $client, 'wikibase_test' );
	}

}
