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
use Silex\Provider\TranslationServiceProvider;
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
        // date_default_timezone_set('Europe/London');
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
        $app->register(new TranslationServiceProvider());
        $app->register(new TranslationServiceProvider());
        $app->register(new DateServiceProvider());

        $app['translator'] = $app->share($app->extend('translator', function($translator, $app) {
            $translator->addResource('array', include(__DIR__ . '/../locales/de.php'), 'de');
            $translator->addResource('array', include(__DIR__ . '/../locales/fr.php'), 'fr');

            return $translator;
        }));

        /**
         * Configures the application
         */
        $app['debug'] = true;
        $app['exception_handler']->disable();

        $app['twig.path'] = __DIR__ . '/../templates';

        $app['system_locales'] = array(
            'en-US' => array(
                'name' => 'American',
                'date_time' => 'n/j/Y g:i:s A', // 4/17/2014 4:32:12 AM
                'short_date' => 'n/j/y',        // 4/17/14
                'medium_date' => 'M j, Y',      // Apr 17, 2014
                'long_date' => 'F j, Y',        // April 17, 2014
                'full_date' => 'l, F j, Y',     // Thursday, April 17, 2014
            ),
            'en' => array(
                'name' => 'English',
                'date_time' => 'd/m/Y H:i:s',   // 17/04/2014 16:32:12
                'short_date' => 'd/m/y',        // 17/04/14
                'medium_date' => 'd-M-Y',       // 17-Apr-2014
                'long_date' => 'j F Y',         // 17 April 2014
                'full_date' => 'j F Y',         // 17 April 2014
            ),
            'de' => array(
                'name' => 'Deutsch',
                'date_time' => 'd.m.Y H:i:s',   // 17.04.2014 16:32:12
                'short_date' => 'd.m.y',        // 17.04.14
                'medium_date' => 'd.m.Y',       // 17.04.2014
                'long_date' => 'j. F Y',        // 17. April 2014
                'full_date' => 'l, j. F Y',     // Donnerstag, 17. April 2014
            ),
            'fr' => array(
                'name' => 'Français',
                'date_time' => 'd/m/Y H:i:s',   // 17/04/2014 16:32:12
                'short_date' => 'd/m/y',        // 17/04/14
                'medium_date' => 'd M Y',       // 17 avril 2014
                'long_date' => 'j F Y',         // 17 avril 2014
                'full_date' => 'l j F Y',       // jeudi 17 avril 2014
            ),
        );
        $app['locale'] = 'en';
        $app['locale_fallbacks'] = array('en');


        /**
         * Defines controller
         */
        $app->get(
            '/{_locale}/{timestamp}',
            function (Application $app, $_locale, $timestamp) {
                $datetime = new \DateTime();
                $datetime->setTimestamp($timestamp);
                return $app['twig']->render('dates.twig', array(
                    'date_str' => date('c', $timestamp),
                    'datetime' => $datetime
                ));
            }
        )->bind('home');

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
                '2014-05-11 23:48:46',
                array(
                    'date_time' => '5/11/2014 11:48:46 PM',
                    'short_date' => '5/11/14',
                    'medium_date' => 'May 11, 2014',
                    'long_date' => 'May 11, 2014',
                    'full_date' => 'Sunday, May 11, 2014',
                )
            ),
            array(
                'en',
                '2014-05-11 23:48:46',
                array(
                    'date_time' => '11/05/2014 23:48:46',
                    'short_date' => '11/05/14',
                    'medium_date' => '11-May-2014',
                    'long_date' => '11 May 2014',
                    'full_date' => '11 May 2014',
                )
            ),
            array(
                'de',
                '2014-05-11 23:48:46',
                array(
                    'date_time' => '11.05.2014 23:48:46',
                    'short_date' => '11.05.14',
                    'medium_date' => '11.05.2014',
                    'long_date' => '11. May 2014',
                    'full_date' => 'Sunday, 11. May 2014',
                )
            ),
            array(
                'fr',
                '2014-05-11 23:48:46',
                array(
                    'date_time' => '11/05/2014 23:48:46',
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
    public function testDates($locale, $date, $formats)
    {
        $timestamp = strtotime($date);
        $crawler = $this->client->request('GET', "/$locale/$timestamp");
        $this->assertEquals(
            trim($crawler->filter('.date-str > .date-time')->text()),
            $formats['date_time']
        );
        $this->assertEquals(
            trim($crawler->filter('.date-str > .short-date')->text()),
            $formats['short_date']
        );
        $this->assertEquals(
            trim($crawler->filter('.date-str > .medium-date')->text()),
            $formats['medium_date']
        );
        $this->assertEquals(
            trim($crawler->filter('.date-str > .long-date')->text()),
            $formats['long_date']
        );
        $this->assertEquals(
            trim($crawler->filter('.date-str > .full-date')->text()),
            $formats['full_date']
        );
        $this->assertEquals(
            trim($crawler->filter('.datetime > .date-time')->text()),
            $formats['date_time']
        );
        $this->assertEquals(
            trim($crawler->filter('.datetime > .short-date')->text()),
            $formats['short_date']
        );
        $this->assertEquals(
            trim($crawler->filter('.datetime > .medium-date')->text()),
            $formats['medium_date']
        );
        $this->assertEquals(
            trim($crawler->filter('.datetime > .long-date')->text()),
            $formats['long_date']
        );
        $this->assertEquals(
            trim($crawler->filter('.datetime > .full-date')->text()),
            $formats['full_date']
        );
    }
}
