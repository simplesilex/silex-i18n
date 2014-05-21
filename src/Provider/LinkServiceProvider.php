<?php

/**
 * Part of the Silex-i18n package.
 *
 * @package   Silex-i18n
 * @copyright 2014 Yuriy Davletshin
 * @license   http://opensource.org/licenses/mit-license/ The MIT License (MIT)
 * @link      http://www.simplesilex.com/
 */
namespace SimpleSilex\SilexI18n\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use SimpleSilex\SilexI18n\LocaleUri;

/**
 * Makes it easy to create locale links.
 *
 * @author Yuriy Davletshin <yuriy.davletshin@gmail.com>
 */
class LinkServiceProvider implements ServiceProviderInterface
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
        $app['i18n_uri'] = $app->share(function () use ($app) {
            return new LocaleUri(
                $app['request']->getRequestUri(),
                $app['system_locales']
            );
        });

        $app->before(function (Request $request) use ($app) {
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

        /**
         * Extends Twig
         */
        $app->extend('twig', function (\Twig_Environment $twig) use ($app) {
            /**
             * Adds the `active_link` function.
             *
             * Use:
             * <ul class="nav">
             *     <li {{- active_link('home') }}>
             *         <a href="{{ path('home') }}">Home</a>
             *     </li>
             *     <li {{- active_link('page') }}>
             *         <a href="{{ path('page') }}">Page</a>
             *     </li>
             * </ul>
             */
            $twig->addFunction(
                new \Twig_SimpleFunction(
                    'active_link',
                    function ($route, $classes = '') use ($app) {
                        $attribute = '';
                        if ($route === $app['request']->get('_route')) {
                            $classes .= ' ' . $app['i18n_link.active_class'];
                        }
                        if ($classes) {
                            $attribute = ' class="' . ltrim($classes) . '"';
                        }
                        return $attribute;
                    },
                    array('is_safe' => array('html'))
                )
            );

            /**
             * Adds the `active_locale` function.
             *
             * Use:
             * <ul class="lang">
             *     {% for locale, params in app.system_locales %}
             *     <li {{- active_locale(locale) }}>
             *         <a href="{{ localelink_path(locale) }}">
             *             {{ params.name }}
             *         </a>
             *     </li>
             *     {% endfor %}
             * </ul>
             */
            $twig->addFunction(
                new \Twig_SimpleFunction(
                    'active_locale',
                    function ($locale, $classes = '') use ($app) {
                        $attribute = '';
                        if ($locale === $app['i18n_uri.locale'] || '' === $app['i18n_uri.locale']) {
                            if ($locale === $app['locale']) {
                                $classes .= ' ' . $app['i18n_link.active_class'];
                            }
                        }
                        if ($classes) {
                            $attribute = ' class="' . ltrim($classes) . '"';
                        }
                        return $attribute;
                    },
                    array('is_safe' => array('html'))
                )
            );

            /**
             * Adds the `localelink_path` function.
             *
             * Use:
             * <ul class="lang">
             *     {% for locale, params in app.system_locales %}
             *     <li {{- active_locale(locale) }}>
             *         <a href="{{ localelink_path(locale) }}">
             *             {{ params.name }}
             *         </a>
             *     </li>
             *     {% endfor %}
             * </ul>
             */
            $twig->addFunction(
                new \Twig_SimpleFunction(
                    'localelink_path',
                    function ($locale) use ($app) {
                        if ('' === $app['i18n_uri.locale'] && $locale === $app['locale']) {
                            $path = $app['i18n_uri.clipped_path'];
                        } else {
                            $path = '/' . $locale . $app['i18n_uri.clipped_path'];
                        }
                        return $path;
                    },
                    array('is_safe' => array('html'))
                )
            );
            return $twig;
        });
    }

    /**
     * {@inheritDoc}
     */
    public function boot(Application $app)
    {
    }
}
