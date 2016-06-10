<?php

namespace Wikibase\Search\ElasticSearch\Mapping;

use Elastica\Type;
use Elastica\Type\Mapping;

/**
 * @license GPL-2.0+
 * @author Katie Filbert < aude.wiki@gmail.com >
 */
class MappingCreator {

	/**
	 * @var MappingPropertiesBuilder
	 */
	private $mappingPropertiesBuilder;

	/**
	 * @param MappingPropertiesBuilder $mappingPropertiesBuilder
	 */
	public function __construct( MappingPropertiesBuilder $mappingPropertiesBuilder ) {
		$this->mappingPropertiesBuilder = $mappingPropertiesBuilder;
	}

	/**
	 * @return Mapping
	 */
	public function createMapping( Type $type ) {
		$properties = $this->mappingPropertiesBuilder->getProperties();

		$mapping = new Mapping( $type, $properties );
		$mapping->setParam( 'dynamic', false );

		return $mapping;
	}

}
