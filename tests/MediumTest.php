<?php

use Battis\ConfigXML;

class MediumTest extends PHPUnit_Framework_TestCase {

	/** @var ConfigXML */
	protected $xml;

	public function __construct() {
		parent::__construct();
		$this->xml = new ConfigXML(__DIR__ . '/medium.xml');
	}

	public function testToString() {
		$this->assertEquals("Value AValue BValue C", $this->xml->toString("//child"));
	}
}
