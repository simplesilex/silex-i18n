<?php

/**
 * Part of the Silex-i18n package.
 *
 * @package   Silex-i18n
 * @copyright 2014 Yuriy Davletshin
 * @license   http://opensource.org/licenses/mit-license/ The MIT License (MIT)
 * @link      http://www.simplesilex.com/
 */
namespace SimpleSilex\SilexI18n\Tests\Twig;

use Silex\WebTestCase;
use Silex\Application;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\UrlGeneratorServiceProvider;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\DomCrawler\Crawler;
use SimpleSilex\SilexI18n\Twig\LinkI18nServiceProvider;

/**
 * Tests LinkServiceProvider.
 *
 * @author Yuriy Davletshin <yuriy.davletshin@gmail.com>
 */
class LinkI18nServiceProviderTest extends WebTestCase
{
    protected $client;
    protected $temp;

    /**
     * PHPUnit setUp.
     */
    public function setUp()
    {
        parent::setUp();
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
        $app->register(new UrlGeneratorServiceProvider());
        $app->register(new LinkI18nServiceProvider());

        /**
         * Configures the application
         */
        $app['debug'] = true;
        $app['exception_handler']->disable();

        $app['twig.path'] = __DIR__ . '/templates';

        $app['system_locales'] = array(
            'en-US' => array(
                'abbr' => 'En',
                'name' => 'English',
            ),
            'fr' => array(
                'abbr' => 'Fr',
                'name' => 'Français',
            ),
            'uk_UA' => array(
                'abbr' => 'Укр',
                'name' => 'Українська',
            ),
        );
        $app['locale'] = 'en-US';

        $app->error(function (\Exception $e) use ($app) {
            return new Response('Fail');
        });

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
     * Assertions for links.
     *
     * @param Crawler $crawler      The instance of Crawler
     * @param string  $listSelector CSS selector of <ul>
     * @param array   $params       An array of parameters
     */
    protected function assertlinks(Crawler $crawler, $listSelector, $params)
    {
        if ('ul.nav' === $listSelector) {
            $selectors = array(
                ' > li' => 3,
                ' > li.class-1' => 2,
                ' > li.class-2' => 1,
            );
        } elseif ('ul.lang' === $listSelector) {
            $selectors = array(
                ' > li' => 3,
                ' > li.lang-item' => 3,
            );
        }
        foreach ($selectors as $selector => $quantity) {
            $this->assertEquals(
                count($crawler->filter($listSelector . $selector)),
                $quantity
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
        $this->assertTrue($this->app instanceof Application);
    }

    /**
     * Tests the Twig Environment.
     */
    public function testTwigEnvironment()
    {
        $this->assertTrue($this->app['twig'] instanceof \Twig_Environment);
    }

    /**
     * Tests Twig extension.
     */
    public function testTwigExtension()
    {
        $this->assertEquals(
            get_class($this->app['twig']->getExtension('link_i18n')),
            'SymfoCode\Twig\I18n\Extension\LinkI18nExtension'
        );
    }

    /**
     * TwigFunctionProvider.
     */
    public function twigFunctionProvider()
    {
        return array(
            array('active_link'),
            array('active_locale'),
            array('active_route')
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
                'http://localhost/en-US/',
                '/en-US/',
                'en-US',
                'Home',
                'En',
            ),
            array(
                'http://localhost/fr/',
                '/fr/',
                'fr',
                'Home',
                'Fr',
            ),
            array(
                'http://localhost/uk_UA/',
                '/uk_UA/',
                'uk_UA',
                'Home',
                'Укр',
            ),
            array(
                'http://localhost/en-US/page/',
                '/en-US/page/',
                'en-US',
                'Page',
                'En',
            ),
            array(
                'http://localhost/fr/page/',
                '/fr/page/',
                'fr',
                'Page',
                'Fr',
            ),
            array(
                'http://localhost/uk_UA/page/',
                '/uk_UA/page/',
                'uk_UA',
                'Page',
                'Укр',
            ),
            // array(
            //     'http://localhost/some/page/',
            //     '/some/page/',
            //     'en-US',
            //     'Some page',
            //     'En',
            // ),
        );
    }

    /**
     * Tests active links.
     *
     * @dataProvider uriProvider
     */
    public function testActiveLink($url, $path, $locale, $content, $abbr)
    {
        $crawler = $this->client->request('GET', $path);
        $this->assertlinks($crawler, 'ul.nav', array(
            'content_of_active_link' => $abbr
        ));
    }

    /**
     * Tests active locale links.
     *
     * @dataProvider uriProvider
     */
    public function testActiveLocale($url, $path, $locale, $content, $abbr)
    {
        $crawler = $this->client->request('GET', $path);
        $this->assertlinks($crawler, 'ul.lang', array(
            'content_of_active_link' => $abbr
        ));
    }

    // *
    //  * Tests localelink paths for lang links.
    //  *
    //  * @dataProvider uriProvider
     
    // public function testLocaleLinkPaths($url, $path, $locale, $content, $abbr)
    // {
    //     $crawler = $this->client->request('GET', $path);
    //     $this->assertEquals(
    //         $crawler->selectLink($abbr)->link()->getUri(),
    //         $url
    //     );
    // }

    /**
     * FailProvider.
     */
    public function failProvider()
    {
        return array(
            array('/es/page/'),
            array('/zz/page/'),
            array('/some/page2/')
        );
    }

    /**
     * Tests localelink paths for lang links.
     *
     * @dataProvider failProvider
     */
    public function testFailPaths($path)
    {
        $crawler = $this->client->request('GET', $path);
        $this->assertFalse($this->client->getResponse()->isOk());
    }
}
