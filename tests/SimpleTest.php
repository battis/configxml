<?php
	
use Battis\ConfigXML;

class SimpleTest extends PHPUnit_Framework_TestCase {
	
	public function testLiteralStringConstructor() {
		$config = new ConfigXML('<root><child>Value</child></root>');
		
		$this->assertEquals(true, $config->isXML());
	}
	
	public function testFilePathConstructor() {
		$config = new ConfigXML(__DIR__ . '/simple.xml');
		
		$this->assertEquals(true, $config->isXML());
	}
	
	public function testURLConstructor() {
		$config = new ConfigXML('https://raw.githubusercontent.com/battis/configxml/develop/tests/simple.xml');
		
		$this->assertEquals(true, $config->isXML());
	}
	
	public function testToArray() {
		$config = new ConfigXML(__DIR__ . '/simple.xml');
		
		$this->assertEquals(array('#document' => array(0 => array('root' => array('child' => array('leaf' => array('@value' => 'Value')))))), $config->toArray('/'));
	}
}