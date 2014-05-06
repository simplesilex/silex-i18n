<?php

namespace SimpleSilex\SilexI18n\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use SimpleSilex\SilexI18n\LocaleUri;

class LinkServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritDoc}
     */
    public function register(Application $app)
    {
        /**
         * Configures Silex application
         */
        $app['i18n_link.active_class'] = 'active';

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
         * Initialize
         */
        $app['i18n_uri'] = $app->share(function () use ($app) {
            return new LocaleUri(
                $app['request']->getRequestUri(),
                $app['system_locales']
            );
        });

        $app->before(function (Request $request) use ($app) {
            $locale = $request->getLocale();
            if (!$app['system_locales'][$locale]) {
                $app->abort(404, 'Locale "' . $locale . '" does not exist.');
            }
            $app['i18n_uri.parsed_path'] = $app['i18n_uri']->getParsedPath();
            $app['i18n_uri.locale'] = $app['i18n_uri']->getParsedLocale();
        });

        $app->error(function (\Exception $e) use ($app) {
            $app['i18n_uri.parsed_path'] = $app['i18n_uri']->getParsedPath();
            $app['i18n_uri.locale'] = $app['i18n_uri']->getParsedLocale();
            $app['locale'] = $app['i18n_uri.locale'];
            $app['request_context']->setParameters(array(
                '_locale' => $app['locale']
            ));
            $app['request']->setLocale($app['locale']);
        });

        /**
         * Extends Twig
         */
        $app->extend('twig', function (\Twig_Environment $twig) use ($app) {
            $twig->addFunction(
                new \Twig_SimpleFunction(
                    'active_link',
                    function ($route, $classes = '') use ($app) {
                        $attribute = '';
                        if ($route === $app['request']->get('_route')) {
                            $classes .= ' ' . $app['i18n_link.active_class'];
                            $attribute = ' class="' . $classes . '"';
                        } elseif ($classes) {
                            $attribute =  ' class="' . $classes . '"';
                        }
                        return $attribute;
                    },
                    array('is_safe' => array('html'))
                )
            );
            $twig->addFunction(
                new \Twig_SimpleFunction(
                    'active_locale',
                    function ($locale, $classes = '') use ($app) {
                        $attribute = '';
                        $expr = ($locale === $app['locale']);
                        if ($locale === $app['i18n_uri.locale'] && $expr) {
                            $classes .= ' ' . $app['i18n_link.active_class'];
                            $attribute = ' class="' . $classes . '"';
                        } elseif ('' === $app['i18n_uri.locale'] && $expr) {
                            $classes .= ' ' . $app['i18n_link.active_class'];
                            $attribute = ' class="' . $classes . '"';
                        } elseif ($classes) {
                            $attribute =  ' class="' . $classes . '"';
                        }
                        return $attribute;
                    },
                    array('is_safe' => array('html'))
                )
            );
            $twig->addFunction(
                new \Twig_SimpleFunction(
                    'localelink_path',
                    function ($locale) use ($app) {
                        $expr = ($locale === $app['locale']);
                        if ('' === $app['i18n_uri.locale'] && $expr) {
                            return $app['i18n_uri.parsed_path'];
                        }
                        return '/' . $locale . $app['i18n_uri.parsed_path'];
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
