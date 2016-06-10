<?php

namespace Wikibase\Search;

/**
 * Interface for searching for entities by terms.
 *
 * @license GPL-2.0+
 * @author Addshore
 * @author Katie Filbert < aude.wiki@gmail.com >
 */
interface EntitySearcher {

	/**
	 * @param string $text Term text to search for
	 * @param string $languageCode Language code to search in
	 * @param string $entityType Type of Entity to return
	 * @param string[] $termTypes Types of Term to return ('label', 'description', 'alias')
	 *
	 * @return TermSearchResult[]
	 */
	public function search( $text, $languageCode, $entityType, array $termTypes );

}
