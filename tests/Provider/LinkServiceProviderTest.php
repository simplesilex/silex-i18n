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
use Silex\Provider\UrlGeneratorServiceProvider;
use Symfony\Component\DomCrawler\Crawler;
use SimpleSilex\SilexI18n\Provider\LinkServiceProvider;

/**
 * Tests LinkServiceProvider.
 *
 * @author Yuriy Davletshin <yuriy.davletshin@gmail.com>
 */
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
        $app->register(new LinkServiceProvider());

        /**
         * Configures the application
         */
        $app['debug'] = true;
        $app['exception_handler']->disable();

        $app['twig.path'] = __DIR__ . '/../templates';

        $app['system_locales'] = array(
            'en' => array(
                'abbr' => 'En',
                'name' => 'English',
            ),
            'fr' => array(
                'abbr' => 'Fr',
                'name' => 'Français',
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

        $app->get('/some/page/', function (Application $app) {
            return $app['twig']->render('links.twig');
        })->bind('some-page');

        $app->get('/{_locale}/page/', function (Application $app) {
            return $app['twig']->render('links.twig');
        })->bind('page');

        return $app;
    }

    /**
     * Gets an instance of Crawler.
     *
     * @param string $uri URI path
     *
     * @return Crawler The instance of Crawler
     */
    protected function getCrawler($uri)
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', $uri);
        $this->assertTrue($client->getResponse()->isOk());

        return $crawler;
    }

    /**
     * Assertions for links.
     *
     * @param Crawler $crawler      The instance of Crawler
     * @param string  $listSelector CSS selector of <ul>
     * @param array   $params       An array of parameters
     */
    protected function assertlinks(Crawler $crawler, $listSelector, $params)
    {
        if ('ul.nav' === $listSelector) {
            $this->assertEquals(
                count($crawler->filter($listSelector . ' > li')),
                3
            );
            $this->assertEquals(
                count($crawler->filter($listSelector . ' > li.class-1')),
                2
            );
            $this->assertEquals(
                count($crawler->filter($listSelector . ' > li.class-2')),
                1
            );
        } elseif ('ul.lang' === $listSelector) {
            $this->assertEquals(
                count($crawler->filter($listSelector . ' > li')),
                3
            );
            $this->assertEquals(
                count($crawler->filter($listSelector . ' > li.lang-item')),
                3
            );
        }
        $this->assertEquals(
            count($crawler->filter($listSelector . ' > li.active')),
            1
        );
        if (isset($params['content_of_active_links'])) {
            $this->assertEquals(
                trim($crawler->filter($listSelector . ' > li.active')->text()),
                $params['content_of_active_links']
            );
        }
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
     * TwigFunctionProvider.
     */
    public function twigFunctionProvider()
    {
        return array(
            array('active_link'),
            array('active_locale'),
            array('localelink_path')
        );
    }

    /**
     * Tests Twig functions.
     *
     * @dataProvider twigFunctionProvider
     */
    public function testTwigFunctions($funcName)
    {
        $this->assertEquals(
            get_class($this->app['twig']->getFunction($funcName)),
            'Twig_SimpleFunction'
        );
    }

    /**
     * UriProvider.
     */
    public function uriProvider()
    {
        return array(
            array(
                '/en/',
                'http://localhost/en/',
                'Home',
                'En',
            ),
            array(
                '/fr/',
                'http://localhost/fr/',
                'Home',
                'Fr',
            ),
            array(
                '/uk/',
                'http://localhost/uk/',
                'Home',
                'Укр',
            ),
            array(
                '/en/page/',
                'http://localhost/en/page/',
                'Page',
                'En',
            ),
            array(
                '/fr/page/',
                'http://localhost/fr/page/',
                'Page',
                'Fr',
            ),
            array(
                '/uk/page/',
                'http://localhost/uk/page/',
                'Page',
                'Укр',
            ),
            array(
                '/some/page/',
                'http://localhost/some/page/',
                'Some page',
                'En',
            ),
        );
    }

    /**
     * Tests active links.
     *
     * @dataProvider uriProvider
     */
    public function testActiveLink($path, $url, $content, $locale)
    {
        $crawler = $this->getCrawler($path);
        $this->assertlinks($crawler, 'ul.nav', array(
            'content_of_active_link' => $content
        ));
    }

    /**
     * Tests active locale links.
     *
     * @dataProvider uriProvider
     */
    public function testActiveLocale($path, $url, $content, $locale)
    {
        $crawler = $this->getCrawler($path);
        $this->assertlinks($crawler, 'ul.lang', array(
            'content_of_active_link' => $locale
        ));
    }

    /**
     * Tests localelink paths for lang links.
     *
     * @dataProvider uriProvider
     */
    public function testLocaleLinkPaths($path, $url, $content, $locale)
    {
        $crawler = $this->getCrawler($path);
        $this->assertEquals(
            $crawler->selectLink($locale)->link()->getUri(),
            $url
        );
    }
}
