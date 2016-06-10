<?php

namespace Wikibase\Search\ElasticSearch\Index;

use Elastica\Document;
use Elastica\Type;
use Wikibase\DataModel\Entity\EntityDocument;
use Wikibase\DataModel\Term\DescriptionsProvider;
use Wikibase\DataModel\Term\LabelsProvider;
use Wikibase\DataModel\Term\TermList;

/**
 * @license GPL-2.0+
 * @author Katie Filbert < aude.wiki@gmail.com >
 */
class EntityIndexer {

	/**
	 * @param Type
	 */
	private $type;

	/**
	 * @param Type $type
	 */
	public function __construct( Type $type ) {
		$this->type = $type;
	}

	public function index( EntityDocument $entity ) {
		$entityId = $entity->getId();

		if ( $entityId === null ) {
			// @todo error
			return;
		}

		$document = new Document( $entityId->getSerialization() );
		$document->setDocAsUpsert( true );

		if ( $entity instanceof LabelsProvider ) {
			$fields = $this->getTermListFields( $entity->getLabels(), 'label' );

			foreach ( $fields as $fieldName => $value ) {
				$document->set( $fieldName, $value );
			}
		}

		if ( $entity instanceof DescriptionsProvider ) {
			$fields = $this->getTermListFields( $entity->getDescriptions(), 'description' );

			foreach ( $fields as $fieldName => $value ) {
				$document->set( $fieldName, $value );
			}
		}

		$this->type->addDocument( $document );
	}

	/**
	 * @param string $fieldName
	 *
	 * @return bool
	 */
	private function isValidField( $fieldName ) {
		$mapping = $this->type->getMapping();
		$typeName = $this->type->getName();

		return array_key_exists( $fieldName, $mapping[$typeName]['properties'] );
	}

	/**
	 * @param TermList $terms
	 * @param string $fieldPrefix
	 */
	private function getTermListFields( TermList $terms, $fieldPrefix ) {
		$fields = [];

		foreach ( $terms as $languageCode => $term ) {
			$fieldName = $fieldPrefix . '_' . $languageCode;
			$fields[$fieldName] = $term->getText();
		}

		return $fields;
	}

}
