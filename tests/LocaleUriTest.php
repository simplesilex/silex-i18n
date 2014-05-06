<?php

namespace SimpleSilex\SilexI18n\Tests;

use SimpleSilex\SilexI18n\LocaleUri;

class LocaleUriTest extends \PHPUnit_Framework_TestCase
{
    protected $testObject;
    protected $testUri;

    /**
     * PHPUnit setUp.
     * Creates an instance of the LocaleUri.
     */
    public function setUp()
    {
        $this->testUri = '/en/some/page/';
        $this->testObject = new LocaleUri($this->testUri);
    }

    /**
     * Tests the instance of the LocaleUri.
     */
    public function testInitObject()
    {
        $this->assertEquals(
            get_class($this->testObject),
            'SimpleSilex\SilexI18n\LocaleUri'
        );
    }

    /**
     * Tests getting URI property.
     */
    public function testGettingUri()
    {
        $this->assertEquals($this->testObject->getUri(), $this->testUri);
    }
}
