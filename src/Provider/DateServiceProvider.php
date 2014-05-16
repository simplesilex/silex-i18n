<?php

/**
 * Part of the Silex-i18n package.
 *
 * @package   SilexLocalizer
 * @copyright 2014 Yuriy Davletshin
 * @license   http://opensource.org/licenses/mit-license/ The MIT License (MIT)
 * @link      http://www.simplesilex.com/
 */
namespace SimpleSilex\SilexI18n\Provider;

use Silex\Application;
use Silex\Translator;
use Silex\ServiceProviderInterface;

/**
 * Makes it easy to create locale dates.
 *
 * @author Yuriy Davletshin <yuriy.davletshin@gmail.com>
 */
class DateServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritDoc}
     */
    public function register(Application $app)
    {
        /**
         * Configures this provider
         */
        $app['system_locales'] = array(
            'en' => array(
                'name' => 'English',
                'date_time' => 'n/j/Y g:i:s A', // 4/17/14 4:32:12 AM
                'short_date' => 'n/j/y',        // 4/17/14
                'medium_date' => 'M j, Y',      // Apr 17, 2014
                'long_date' => 'F j, Y',        // April 17, 2014
                'full_date' => 'l, F j, Y',     // Thursday, April 17, 2014
            ),
        );
        $app['locale'] = 'en';
        $app['locale_fallbacks'] = array($app['locale']);

        /**
         * Extends Twig
         */
        $app->extend('twig', function (\Twig_Environment $twig) use ($app) {
            /**
             * Adds the `localedate` filter.
             *
             * Use:
             * <div>{{ entity.date|localedate }}</div>
             * or:
             * <div>{{ entity.date|localedate('short_date') }}</div>
             */
            $twig->addFilter(
                new \Twig_SimpleFilter(
                    'localedate',
                    function ($date, $type = null) use ($twig, $app) {
                        $convert = function ($date, $format) use ($twig, $app) {
                            return twig_date_format_filter(
                                $twig,
                                $date,
                                $format,
                                null // TODO: Add timezone support.
                            );
                        };

                        $map = function ($element) use ($app, $convert, $date) {
                            $char = '/[dDjlNSwzWFmMntLoYyaABgGhHisueIOPTZcrU]/';
                            if (preg_match($char, $element)) {
                                $value = $convert($date, $element);
                                if (preg_match('/[DlMF]/', $element)) {
                                    $value = $app['translator']->trans($value);
                                }
                            } else {
                                $value = $element;
                            }
                            return $value;
                        };

                        $locale = $app['system_locales'][$app['locale']];
                        if (null === $type || !isset($locale[$type])) {
                            return $convert($date, null);
                        }
                        $format = $locale[$type];
                        $expr = (isset($app['translator']));
                        if (!preg_match('/[DlMF]/', $format) || !$expr) {
                            return $convert($date, $format);
                        }
                        $result = '';
                        for ($i = 0; $i < mb_strlen($format, 'UTF-8'); $i++) {
                            $elements[] = mb_substr($format, $i, 1, "UTF-8");
                        }
                        $result = array_map($map, $elements);

                        return implode($result);
                    }
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
