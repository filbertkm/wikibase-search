<?php

namespace Wikibase\Search\ElasticSearch\Mapping;

/**
 * @license GPL-2.0+
 * @author Katie Filbert < aude.wiki@gmail.com >
 */
class MappingPropertiesBuilder {

	/**
	 * @var string[]
	 */
	private $languageCodes;

	/**
	 * @param string[] $languageCodes
	 */
	public function __construct( array $languageCodes ) {
		$this->languageCodes = $languageCodes;
	}

	/**
	 * @return array
	 */
	public function getProperties() {
		$properties = [];

		foreach ( $this->languageCodes as $languageCode ) {
			$properties['label_' . $languageCode] = $this->getTextFieldMapping();
			$properties['description_' . $languageCode] = $this->getTextFieldMapping();
		}

		ksort( $properties );

		return $properties;
	}

	/**
	 * @return array
	 */
	private function getTextFieldMapping() {
		$fieldMapping = [
			'type' => 'string'
		];

		return $fieldMapping;
	}

}
