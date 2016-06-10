<?php

namespace Wikibase\Search\Tests\Integration\ElasticSearch;

use Elastica\Client;
use Elastica\Index;
use Elastica\Type;
use Wikibase\Search\ElasticSearch\Mapping\MappingCreator;
use Wikibase\Search\ElasticSearch\Mapping\MappingPropertiesBuilder;

/**
 * @licence GNU GPL v2+
 * @author Katie Filbert < aude.wiki@gmail.com >
 */
class MappingCreationTest extends \PHPUnit_Framework_TestCase {

	protected function setUp() {
		parent::setUp();

		$this->getTestIndex()->create( [], [ 'recreate' => true ] );
	}

	protected function tearDown() {
		$this->getTestIndex()->delete();
	}

	public function testCreateMapping() {
		$mappingPropertiesBuilder = new MappingPropertiesBuilder( [ 'ar' ] );
		$mappingCreator = new MappingCreator( $mappingPropertiesBuilder );

		$index = $this->getTestIndex();
		$type = new Type( $index, 'entities' );

		$mapping = $mappingCreator->createMapping( $type );
		$mapping->send();

		$expected = [
			'entities' => [
				'dynamic' => 'false',
				'properties' => [
					'description_ar' => [
						'type' => 'string'
					],
					'label_ar' => [
						'type' => 'string'
					]
				]
			]
		];

		$this->assertSame( $expected, $type->getMapping(), 'mapping from type, after send' );
	}

	private function getTestIndex() {
		$client = new Client( [
			'servers' => [
				[
					'host' => '127.0.0.1'
				]
			]
		] );

		return new Index( $client, 'wikibase_test' );
	}

}
