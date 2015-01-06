<?php

/**
 * Part of the Silex-i18n package.
 *
 * @package   Silex-i18n
 * @copyright 2014 Yuriy Davletshin
 * @license   http://opensource.org/licenses/mit-license/ The MIT License (MIT)
 * @link      http://www.simplesilex.com/
 */
namespace SimpleSilex\SilexI18n\Twig;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use SimpleSilex\SilexI18n\LocaleUri;
use SymfoCode\Twig\I18n\Extension\LinkI18nExtension;

/**
 * Makes it easy to create locale links.
 *
 * @author Yuriy Davletshin <yuriy.davletshin@gmail.com>
 */
class LinkI18nServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritDoc}
     */
    public function register(Application $app)
    {
        /**
         * Configures this provider
         */
        $app['i18n_link.active_class'] = 'active';

        $app['system_locales'] = array(
            'en' => array(
                'abbr' => 'En',
                'name' => 'English',
            ),
        );
        $app['locale'] = 'en';

        /**
         * Initializes this provider
         */
        $app['request_stack'] = $app->share(function () use ($app) {
            $requestStack = new RequestStack();
            // $requestStack->push($app['request']);

            return $requestStack;
        });

        $app['twig'] = $app->extend('twig', function (\Twig_Environment $twig) use ($app) {
            $twig->addExtension(new LinkI18nExtension($app['request_stack'], $app['i18n_link.active_class']));

            return $twig;
        });

        $app['i18n_uri'] = $app->share(function () use ($app) {
            return new LocaleUri(
                $app['request']->getRequestUri(),
                $app['system_locales']
            );
        });

        $app->before(function (Request $request) use ($app) {
            $app['request_stack']->push($request);
            $locale = $request->getLocale();
            if (!isset($app['system_locales'][$locale])) {
                return $app->abort(404, 'Locale "' . $locale . '" does not exist.');
            }
            $app['i18n_uri.clipped_path'] = $app['i18n_uri']->getClippedPath();
            $app['i18n_uri.locale'] = $app['i18n_uri']->getLocale();
        });

        $app->error(function (\Exception $e) use ($app) {
            $app['i18n_uri.clipped_path'] = $app['i18n_uri']->getClippedPath();
            $app['i18n_uri.locale'] = $app['i18n_uri']->getLocale();
            $app['locale'] = $app['i18n_uri.locale'];
            $app['request_context']->setParameters(array('_locale' => $app['locale']));
            $app['request']->setLocale($app['locale']);
        });

    }

    /**
     * {@inheritDoc}
     */
    public function boot(Application $app)
    {
    }
}
