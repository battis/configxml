<?php

use Battis\ConfigXML;

class MediumTest extends PHPUnit_Framework_TestCase {

	public function testToString() {
		$config = new ConfigXML(__DIR__ . '/medium.xml');

		$this->assertEquals("Value AValue BValue C", $config->toString("//child"));
	}
}
