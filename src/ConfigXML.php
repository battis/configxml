<?php
	
namespace Battis;

use DOMDocument;
use DOMXPath;
use ReflectionClass;

/**
 * A simple wrapper to work quickly with an XML configuration for an app
 *
 * @author Seth Battis <seth@battis.net>
 */
class ConfigXML {
	
	/** @var DOMDocument The loaded configuration*/
	private $dom;
	
	/** @var DOMXpath XPath query manager */
	private $xpath;
	
	/**
	 * Constructor
	 * 
	 * @param string $configuration Literal XML string or a path string to an XML
	 *		file
	 */
	public function __construct($configuration) {
		$configuration = (string) $configuration;
		if (realpath($configuration)) {
			$configuration = file_get_contents($configuration);
		}
		$this->dom = DOMDocument::loadXML($configuration);
		$this->xpath = new DOMXPath($this->dom);
	}
	
	/**
	 * Extract an XPath query as an associative array
	 * 
	 * @param string $query XPath query
	 *
	 * @return array An array of matches converted to associative arrays. `null`
	 *		if no matches found.
	 */
	public function toArray($query) {
		$nodes = $this->xpath((string) $query);
		if ($nodes->length) {
			$result = array();
			foreach($nodes as $node) {
				$result[] = json_decode(
					json_encode(
						simplexml_load_string(
							$this->dom->saveXML($node)
						)
					),
					true
				);
			}
			return $result;
		}
		return null;
	}
	
	/**
	 * Create an instance of an object with paramters from the configuration
	 * 
	 * @param string $class The class name (as in `mysqli::class` or
	 *		`'\Battis\AppMetadata'
	 * @param string $query XPath query
	 * @param int $n Which match index from the XPath query to use (default: '0`)
	 *
	 * @return mixed An instance of `$class` constructed from the nth match to
	 *		`$query` 
	 */
	public function newInstanceOf($class, $query, $n = 0) {
		return (new ReflectionClass($class))->newInstanceArgs($this->toArray($query)[$n]);
	}
}