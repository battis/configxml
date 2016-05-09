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

	const ATTRIBUTES = '@attributes';
	const VALUE = '@value';

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
		if (realpath($configuration) || intval(static::getHTTPResponseCode($configuration)) < 400) {
			$configuration = file_get_contents($configuration);
		}
		$this->dom = new DOMDocument();
		$this->dom->loadXML($configuration);
		$this->xpath = new DOMXPath($this->dom);
	}

	private function getHTTPResponseCode($url) {
		$headers = get_headers($url);
		return substr($headers[0], 9, 3);
	}

	/**
	* @return boolean
	*/
	public function isXML() {
		return is_a($this->dom, DOMDocument::class);
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
		$nodes = $this->xpath->query((string) $query);
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
* Return the text contents of the node(s) selected by the query.
*
* @param  string $query XPath query
* @param  string $glue  (Optional) Glue to hold together imploded list of node values (default: `''`)
* @return string
*/
public function toString($query, $glue = '') {
	$nodes = $this->toArray($query);
	return static::recursiveImplode($glue, $nodes);
}

private static function recursiveImplode($glue, $pieces) {
	if (is_array($pieces)) {
		$strings = array();
		foreach ($pieces as $key => $piece) {
			if (is_int($key) || $key !== self::ATTRIBUTES) {
				if (is_array($piece)) {
					$strings[$key] = static::recursiveImplode($glue, $piece);
				} else {
					$strings[$key] = (string) $piece;
				}
			}
		}
		return implode($glue, $strings);
	} else {
		return (string) $pieces;
	}
}

// based on http://www.akchauhan.com/convert-xml-to-array-using-dom-extension-in-php5/
private static function DOMtoArray($node) {
	$occurence = array();
	$result = null;

	if ($node->childNodes) {
		foreach($node->childNodes as $child) {
			if (isset($occurence[$child->nodeName])) {
				$occurence[$child->nodeName]++;
			} else {
				$occurence[$child->nodeName] = 1;
			}
		}
	}

	if($node->nodeType == XML_TEXT_NODE || $node->nodeType == XML_CDATA_SECTION_NODE) {
		$result = html_entity_decode(
		htmlentities(
		(string) $node->nodeValue,
		ENT_COMPAT,
		'UTF-8'
	),
	ENT_COMPAT,
	'ISO-8859-15'
);
}
else {
	if($node->hasChildNodes()){
		$children = $node->childNodes;

		for($i=0; $i<$children->length; $i++) {
			$child = $children->item($i);

			switch($child->nodeName) {
				case '#cdata-section':
				case '#text':
				$text = static::DOMtoArray($child);

				if (trim($text) != '') {
					$result[self::VALUE] = static::DOMtoArray($child);
				};
				break;

				default:
				if($occurence[$child->nodeName] > 1) {
					$result[$child->nodeName][] = static::DOMtoArray($child);
				}
				else {
					$result[$child->nodeName] = static::DOMtoArray($child);
				}
			}
		}
	}

	if($node->hasAttributes()) {
		$attributes = $node->attributes;

		if(!is_null($attributes)) {
			foreach ($attributes as $key => $attr) {
				$result[self::ATTRIBUTES][$attr->name] = $attr->value;
			}
		}
	}
}

return $result;
}

/**
* How many matches are there for a query?
*
* @param string $query XPath query
*
* @return int
*/
public function count($query) {
	return $this->xpath->query((string) $query)->length;
}

/**
* Create an instance of an object with paramters from the configuration
*
* @param string $class The class name (as in `mysqli::class` or
*		`'\Battis\AppMetadata'
* @param string $query XPath query
* @param int $n Which match index from the XPath query to use (default: `0`)
*
* @return mixed An instance of `$class` constructed from the nth match to
*		`$query`
*/
public function newInstanceOf($class, $query, $n = 0) {
	return (new ReflectionClass($class))->newInstanceArgs($this->toArray($query)[$n]);
}
}
