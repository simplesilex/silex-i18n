<?php

/**
 * Part of the Silex-i18n package.
 *
 * @package   SilexLocalizer
 * @copyright 2014 Yuriy Davletshin
 * @license   http://opensource.org/licenses/mit-license/ The MIT License (MIT)
 * @link      http://www.simplesilex.com/
 */

namespace SimpleSilex\SilexI18n\Tests\Provider;

use Silex\WebTestCase;
use Silex\Application;
use Silex\Provider\TwigServiceProvider;
use SimpleSilex\SilexI18n\Provider\DateServiceProvider;

/**
 * Tests DateServiceProvider.
 *
 * @author Yuriy Davletshin <yuriy.davletshin@gmail.com>
 */
class DateServiceProviderTest extends WebTestCase
{
    protected $client;

    /**
     * PHPUnit setUp.
     */
    public function setUp()
    {
        parent::setUp();
        date_default_timezone_set('Europe/London');
        $this->client = $this->createClient();
    }

    /**
     * Gets an instance of Silex application.
     *
     * @return Application The instance of the application
     */
    public function createApplication()
    {
        /**
         * Creates the application under test
         */
        $app = new Application();

        /**
         * Registers some providers
         */
        $app->register(new TwigServiceProvider());
        $app->register(new DateServiceProvider());

        /**
         * Configures the application
         */
        $app['debug'] = true;
        $app['exception_handler']->disable();

        $app['twig.path'] = __DIR__ . '/../templates';

        $app['system_locales'] = array(
            'en-US' => array(
                'name' => 'American',
                'datetime' => 'n/j/Y H:i:s',
                'short_date' => 'n/j/y', // 4/17/14
                'medium_date' => 'M j, Y', // Apr 17, 2014
                'long_date' => 'F j, Y', // April 17, 2014
                'full_date' => 'l, F j, Y', //Thursday, April 17, 2014
            ),
            'en' => array(
                'name' => 'English',
                'datetime' => 'd/m/Y H:i:s',
                'short_date' => 'd/m/y', // 17/04/14
                'medium_date' => 'd-M-Y', // 17-Apr-2014
                'long_date' => 'j F Y', // 17 April 2014
                'full_date' => 'j F Y', // 17 April 2014
            ),
            'de' => array(
                'name' => 'Deutsch',
                'datetime' => 'd.m.Y H:i:s',
                'short_date' => 'd.m.y', // 17.04.14
                'medium_date' => 'd.m.Y', // 17.04.2014
                'long_date' => 'j. F Y', // 17. April 2014
                'full_date' => 'l, j. F Y', // Donnerstag, 17. April 2014
            ),
            'fr' => array(
                'name' => 'Français',
                'datetime' => 'd/m/Y H:i:s',
                'short_date' => 'd/m/y', // 17/04/14
                'medium_date' => 'd M Y', // 17 Apr 2014
                'long_date' => 'j F Y', // 17 Avril 2014
                'full_date' => 'l j F Y', // Jeudi 17 Avril 2014
            ),
        );

        /**
         * Defines some controllers
         */
        $app->get('/{_locale}/', function (Application $app, $_locale) {
            return $app['twig']->render('dates.twig', array(
                'test_date' => '2014-05-11 23:48:46',
            ));
        })->bind('home');

        return $app;
    }

    /**
     * Tests the Silex application.
     */
    public function testInitApplication()
    {
        $this->assertEquals(get_class($this->app), 'Silex\Application');
    }

    /**
     * Tests the Twig Environment.
     */
    public function testTwigEnvironment()
    {
        $this->assertEquals(get_class($this->app['twig']), 'Twig_Environment');
    }

    /**
     * Tests Twig filter.
     */
    public function testTwigFilter()
    {
        $this->assertEquals(
            get_class($this->app['twig']->getFilter('localedate')),
            'Twig_SimpleFilter'
        );
    }

    /**
     * DateProvider.
     */
    public function dateProvider()
    {
        return array(
            array(
                'en-US',
                array(
                    'datetime' => '5/11/2014 23:48:46',
                    'short_date' => '5/11/14',
                    'medium_date' => 'May 11, 2014',
                    'long_date' => 'May 11, 2014',
                    'full_date' => 'Sunday, May 11, 2014',
                )
            ),
            array(
                'en',
                array(
                    'datetime' => '11/05/2014 23:48:46',
                    'short_date' => '11/05/14',
                    'medium_date' => '11-May-2014',
                    'long_date' => '11 May 2014',
                    'full_date' => '11 May 2014',
                )
            ),
            array(
                'de',
                array(
                    'datetime' => '11.05.2014 23:48:46',
                    'short_date' => '11.05.14',
                    'medium_date' => '11.05.2014',
                    'long_date' => '11. May 2014',
                    'full_date' => 'Sunday, 11. May 2014',
                )
            ),
            array(
                'fr',
                array(
                    'datetime' => '11/05/2014 23:48:46',
                    'short_date' => '11/05/14',
                    'medium_date' => '11 May 2014',
                    'long_date' => '11 May 2014',
                    'full_date' => 'Sunday 11 May 2014',
                )
            ),
        );
    }

    /**
     * Tests date formats.
     *
     * @dataProvider dateProvider
     */
    public function testDates($locale, $formats)
    {
        $crawler = $this->client->request('GET', "/$locale/");
        $this->assertEquals(
            trim($crawler->filter('div.datetime')->text()),
            $formats['datetime']
        );
        $this->assertEquals(
            trim($crawler->filter('div.short-date')->text()),
            $formats['short_date']
        );
        $this->assertEquals(
            trim($crawler->filter('div.medium-date')->text()),
            $formats['medium_date']
        );
        $this->assertEquals(
            trim($crawler->filter('div.long-date')->text()),
            $formats['long_date']
        );
        $this->assertEquals(
            trim($crawler->filter('div.full-date')->text()),
            $formats['full_date']
        );
    }
}
