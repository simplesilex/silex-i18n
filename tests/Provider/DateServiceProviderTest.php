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
        $app->register(new TranslationServiceProvider(), array(
            'locale_fallbacks' => array('en'),
        ));
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

        $app['translator.domains'] = array(
            'date' => array(
                'en-US' => array(
                    'January' => 'January',
                    'Jan' => 'Jan',
                    'February' => 'February',
                    'Feb' => 'Feb',
                    'March' => 'March',
                    'Mar' => 'Mar',
                    'April' => 'April',
                    'Apr' => 'Apr',
                    'May' => 'May',
                    'June' => 'June',
                    'Jun' => 'Jun',
                    'July' => 'July',
                    'Jul' => 'Jul',
                    'August' => 'August',
                    'Aug' => 'Aug',
                    'September' => 'September',
                    'Sep' => 'Sep',
                    'October' => 'October',
                    'Oct' => 'Oct',
                    'November' => 'November',
                    'Nov' => 'Nov',
                    'December' => 'December',
                    'Dec' => 'Dec',
                    'Sunday' => 'Sunday',
                    'Sun' => 'Sun',
                    'Monday' => 'Monday',
                    'Mon' => 'Mon',
                    'Tuesday' => 'Tuesday',
                    'Tue' => 'Tue',
                    'Wednesday' => 'Wednesday',
                    'Wed' => 'Wed',
                    'Thursday' => 'Thursday',
                    'Thu' => 'Thu',
                    'Friday' => 'Friday',
                    'Fri' => 'Fri',
                    'Saturday' => 'Saturday',
                    'Sat' => 'Sat',
                ),
                'en' => array(
                    'January' => 'January',
                    'Jan' => 'Jan',
                    'February' => 'February',
                    'Feb' => 'Feb',
                    'March' => 'March',
                    'Mar' => 'Mar',
                    'April' => 'April',
                    'Apr' => 'Apr',
                    'May' => 'May',
                    'June' => 'June',
                    'Jun' => 'Jun',
                    'July' => 'July',
                    'Jul' => 'Jul',
                    'August' => 'August',
                    'Aug' => 'Aug',
                    'September' => 'September',
                    'Sep' => 'Sep',
                    'October' => 'October',
                    'Oct' => 'Oct',
                    'November' => 'November',
                    'Nov' => 'Nov',
                    'December' => 'December',
                    'Dec' => 'Dec',
                    'Sunday' => 'Sunday',
                    'Sun' => 'Sun',
                    'Monday' => 'Monday',
                    'Mon' => 'Mon',
                    'Tuesday' => 'Tuesday',
                    'Tue' => 'Tue',
                    'Wednesday' => 'Wednesday',
                    'Wed' => 'Wed',
                    'Thursday' => 'Thursday',
                    'Thu' => 'Thu',
                    'Friday' => 'Friday',
                    'Fri' => 'Fri',
                    'Saturday' => 'Saturday',
                    'Sat' => 'Sat',
                ),
                'de' => array(
                    'January' => 'Januar',
                    'Jan' => 'Jan',
                    'February' => 'Februar',
                    'Feb' => 'Feb',
                    'March' => 'März',
                    'Mar' => 'Mär',
                    'April' => 'April',
                    'Apr' => 'Apr',
                    'May' => 'Mai',
                    'June' => 'Juni',
                    'Jun' => 'Jun',
                    'July' => 'Juli',
                    'Jul' => 'Jul',
                    'August' => 'August',
                    'Aug' => 'Aug',
                    'September' => 'September',
                    'Sep' => 'Sep',
                    'October' => 'Oktober',
                    'Oct' => 'Okt',
                    'November' => 'November',
                    'Nov' => 'Nov',
                    'December' => 'Dezember',
                    'Dec' => 'Dez',
                    'Sunday' => 'Sonntag',
                    'Sun' => 'So',
                    'Monday' => 'Montag',
                    'Mon' => 'Mo',
                    'Tuesday' => 'Dienstag',
                    'Tue' => 'Di',
                    'Wednesday' => 'Mittwoch',
                    'Wed' => 'Mi',
                    'Thursday' => 'Donnerstag',
                    'Thu' => 'Do',
                    'Friday' => 'Freitag',
                    'Fri' => 'Fr',
                    'Saturday' => 'Samstag',
                    'Sat' => 'Sa',
                ),
                'fr' => array(
                    'January' => 'janvier',
                    'Jan' => 'janv.',
                    'February' => 'février',
                    'Feb' => 'févr.',
                    'March' => 'mars',
                    'Mar' => 'mars',
                    'April' => 'avril',
                    'Apr' => 'avril',
                    'May' => 'mai',
                    'June' => 'juin',
                    'Jun' => 'juin',
                    'July' => 'juillet',
                    'Jul' => 'juil.',
                    'August' => 'août',
                    'Aug' => 'août',
                    'September' => 'septembre',
                    'Sep' => 'sept.',
                    'October' => 'octobre',
                    'Oct' => 'oct.',
                    'November' => 'novembre',
                    'Nov' => 'nov.',
                    'December' => 'décembre',
                    'Dec' => 'déc.',
                    'Sunday' => 'dimanche',
                    'Sun' => 'dim.',
                    'Monday' => 'lundi',
                    'Mon' => 'lun.',
                    'Tuesday' => 'mardi',
                    'Tue' => 'mar.',
                    'Wednesday' => 'mercredi',
                    'Wed' => 'mer.',
                    'Thursday' => 'jeudi',
                    'Thu' => 'jeu.',
                    'Friday' => 'vendredi',
                    'Fri' => 'ven.',
                    'Saturday' => 'samedi',
                    'Sat' => 'sam.',
                ),
            ),
        );

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
                    'datetime' => '5/11/2014 23:48:46',
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
                    'datetime' => '11/05/2014 23:48:46',
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
                    'datetime' => '11.05.2014 23:48:46',
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
    public function testDates($locale, $date, $formats)
    {
        $timestamp = strtotime($date);
        $crawler = $this->client->request('GET', "/$locale/$timestamp");
        $this->assertEquals(
            trim($crawler->filter('.date-str > .date-time')->text()),
            $formats['datetime']
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
            $formats['datetime']
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
