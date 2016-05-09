<?php

use Battis\ConfigXML;

class ComplexTest extends PHPUnit_Framework_TestCase {

	/** @var ConfigXML */
	protected $xml;

	public function __construct() {
		parent::__construct();
		$this->xml = new ConfigXML(__DIR__ . '/complex.xml');
	}

	public function testToString() {
		$this->assertEquals('a:2:{i:0;s:12:"announcement";i:1;s:20:"conversation_message";}', $this->xml->toString('//option[@name="notification-list"]'));
	}
}
