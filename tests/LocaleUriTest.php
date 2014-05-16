<?php

/**
 * Part of the Silex-i18n package.
 *
 * @package   SilexLocalizer
 * @copyright 2014 Yuriy Davletshin
 * @license   http://opensource.org/licenses/mit-license/ The MIT License (MIT)
 * @link      http://www.simplesilex.com/
 */
namespace SimpleSilex\SilexI18n\Tests;

use SimpleSilex\SilexI18n\LocaleUri;

/**
 * Tests parsing request URI.
 *
 * @author Yuriy Davletshin <yuriy.davletshin@gmail.com>
 */
class LocaleUriTest extends \PHPUnit_Framework_TestCase
{
    protected $locales;
    protected $testObject;

    /**
     * PHPUnit setUp.
     */
    public function setUp()
    {
        $this->locales = array(
            'en_US' => array('name' => 'English'),
            'en-GB' => array('name' => 'English'),
            'fr'    => array('name' => 'Français'),
            'ukr'   => array('name' => 'Українська'),
        );
    }

    /**
     * Creates an instance of LocaleUri.
     */
    protected function init($uri)
    {
        $this->testObject = new LocaleUri($uri, $this->locales);
    }

    /**
     * Tests the instance of LocaleUri.
     */
    public function testInitObject()
    {
        $this->init('/some/page/');
        $this->assertEquals(
            get_class($this->testObject),
            'SimpleSilex\SilexI18n\LocaleUri'
        );
    }

    /**
     * DataProvider.
     */
    public function uriProvider()
    {
        return array(
            array('/en_US/some/page/',   '/some/page/',        'en_US'),
            array('/en-GB/some/page/',   '/some/page/',        'en-GB'),
            array('/fr/some/page',       '/some/page',         'fr'),
            array('/ukr/some/page.html', '/some/page.html',    'ukr'),
            array('/zz/some/page.html',  '/zz/some/page.html', ''),
            array('/some/file.json',     '/some/file.json',    ''),
        );
    }

    /**
     * Tests getting `uri` property.
     *
     * @dataProvider uriProvider
     */
    public function testGettingUri($uri, $path, $locale)
    {
        $this->init($uri);
        $this->assertEquals($this->testObject->getUri(), $uri);
    }

    /**
     * Tests getting `clippedPath` property.
     *
     * @dataProvider uriProvider
     */
    public function testGettingClippedPath($uri, $path, $locale)
    {
        $this->init($uri);
        $this->assertEquals($this->testObject->getClippedPath(), $path);
    }

    /**
     * Tests getting `locale` property.
     *
     * @dataProvider uriProvider
     */
    public function testGettingLocale($uri, $path, $locale)
    {
        $this->init($uri);
        $this->assertEquals($this->testObject->getLocale(), $locale);
    }
}
