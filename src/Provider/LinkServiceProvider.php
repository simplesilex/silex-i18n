<?php

namespace SimpleSilex\SilexI18n\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;

class LinkServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritDoc}
     */
    public function register(Application $app)
    {
        /**
         * Configures the application
         */
        $app['i18n_link.active_class'] = 'active';

        /**
         * Extends Twig
         */
        $app->extend('twig', function (\Twig_Environment $twig) use ($app) {
            $twig->addFunction(
                new \Twig_SimpleFunction(
                    'active_link',
                    function ($route, $class = '') use ($app) {
                        if ($route === $app['request']->get('_route')) {
                            return ' class="' . $class . ' ' .
                                $app['i18n_link.active_class'] . '"';
                        }
                        if ($class) {
                            return ' class="' . $class . '"';
                        }

                        return '';
                    },
                    array('is_safe' => array('html'))
                )
            );
            $twig->addFunction(
                new \Twig_SimpleFunction(
                    'active_locale',
                    function () use ($app) {
                        return;
                    },
                    array('is_safe' => array('html'))
                )
            );
            $twig->addFunction(
                new \Twig_SimpleFunction(
                    'localelink_path',
                    function () use ($app) {
                        return;
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
