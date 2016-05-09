<?php

use Battis\ConfigXML;

class Wrapper {
	protected $value;
	public function __construct(...$param) {
		$this->value = $param[0];
	}
	public function getValue() {
		return $this->value;
	}
}

class SimpleTest extends PHPUnit_Framework_TestCase {

	/** @var ConfigXML */
	protected $xml;

	public function __construct() {
		parent::__construct();
		$this->xml = new ConfigXML(__DIR__ . '/simple.xml');
	}

	public function testLiteralStringConstructor() {
		$this->assertEquals(true, $this->xml->isXML());
	}

	public function testFilePathConstructor() {
		$this->assertEquals(true, $this->xml->isXML());
	}

	public function testURLConstructor() {
		$xml = new ConfigXML('https://raw.githubusercontent.com/battis/configxml/develop/tests/simple.xml');
		$this->assertEquals(true, $xml->isXML());
	}

	/**
	 * @expectedException PHPUnit_Framework_Error
	 */
	public function testFailingURLConstructor() {
		$xml = new ConfigXML('http://battis.net/intentionally-nonexistent-url.xml');
	}

	public function testToArray() {
		$this->assertEquals(array(0 => array('child' => array('leaf' => 'Value'))), $this->xml->toArray('/'));
	}

	public function testToString() {
		$this->assertEquals("Value", $this->xml->toString("//child"));
	}

	public function testNewInstanceOf() {
		$wrapper = $this->xml->newInstanceOf(Wrapper::class, '//leaf');
		$this->assertInstanceOf(Wrapper::class, $wrapper);
		$this->assertEquals("Value", $wrapper->getValue());
	}
}
