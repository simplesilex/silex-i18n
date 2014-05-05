<?php

namespace SimpleSilex\SilexI18n\Tests\Provider;

use Silex\WebTestCase;
use Silex\Application;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\UrlGeneratorServiceProvider;

class LinkServiceProviderTest extends WebTestCase
{
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
        $app->register(new UrlGeneratorServiceProvider());

        /**
         * Configures the application
         */
        $app['debug'] = true;
        $app['exception_handler']->disable();

        $app['twig.path'] = __DIR__ . '/../templates';

        $app['system_locales'] = array(
            'en' => array(
                'abbr' => 'Eng',
                'name' => 'English',
            ),
            'ru' => array(
                'abbr' => 'Рус',
                'name' => 'Руссский',
            ),
            'uk' => array(
                'abbr' => 'Укр',
                'name' => 'Українська',
            ),
        );
        $app['locale_default'] = 'en';

        /**
         * Defines some controllers
         */
        $app->get('/{_locale}/', function (Application $app) {
            return $app['twig']->render('links.twig');
        })->bind('home');

        $app->get('/{_locale}/page/', function (Application $app) {
            return $app['twig']->render('links.twig');
        })->bind('page');

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
}
