<?php
	
namespace Battis;

use DOMDocument;
use DOMXPath;
use ReflectionClass;

class ConfigXML {
	
	/** @var DOMDocument */
	private $dom;
	
	/** @var DOMXpath */
	private $xpath;
	
	public function __construct($configuration) {
		$configuration = (string) $configuration;
		if (realpath($configuration)) {
			$configuration = file_get_contents($configuration);
		}
		$this->dom = DOMDocument::loadXML($configuration);
		$this->xpath = new DOMXPath($this->dom);
	}
	
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
	
	public function newInstanceOf($class, $query) {
		return (new ReflectionClass($class))->newInstanceArgs($this->toArray($query)[0]);
	}
}