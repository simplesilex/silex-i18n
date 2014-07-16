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
        // default locale
        $app['locale'] = 'en';
        $app['locale_fallbacks'] = function () use ($app) {
            return array($app['locale']);
        };
        $app['encoding'] = 'UTF-8';

        /**
         * Extends Twig
         */
        $app->extend('twig', function (\Twig_Environment $twig) use ($app) {
            /**
             * Adds the `localedate` filter.
             *
             * Use:
             * <div>
             *     {{ entity.date|localedate }}
             * </div>
             *
             * or:
             * <div>
             *     {{ entity.date|localedate('short_date') }}
             * </div>
             *
             * or:
             * <div>
             *    {{ entity.date|localedate('short_date', 'Europe/London') }}
             * </div>
             */
            $twig->addFilter(
                new \Twig_SimpleFilter(
                    'localedate',
                    function ($date, $formatType = null, $timezone = null) use ($twig, $app) {
                        /**
                         * Converts a date according to a format.
                         */
                        $convert = function ($date, $format) use ($twig, $app, $timezone) {
                            return twig_date_format_filter($twig, $date, $format, $timezone);
                        };

                        /**
                         * Translates names of months and days of week.
                         */
                        $map = function ($symbol) use ($app, $convert, $date) {
                            $pattern = '/[dDjlNSwzWFmMntLoYyaABgGhHisueIOPTZcrU]/';
                            if (preg_match($pattern, $symbol)) {
                                $value = $convert($date, $symbol);
                                if (preg_match('/[DlMF]/', $symbol)) {
                                    $value = $app['translator']->trans($value);
                                }
                            } else {
                                $value = $symbol;
                            }
                            return $value;
                        };

                        $locale = $app['system_locales'][$app['locale']];
                        if (null === $formatType || !isset($locale[$formatType])) {
                            $result = $convert($date, null);
                        } else {
                            $format = $locale[$formatType];
                            if (!preg_match('/[DlMF]/', $format) || !isset($app['translator'])) {
                                $result = $convert($date, $format);
                            } else {
                                for ($i = 0; $i < mb_strlen($format, $app['encoding']); $i++) {
                                    $symbols[] = mb_substr($format, $i, 1, $app['encoding']);
                                }
                                $result = implode(array_map($map, $symbols));
                            }
                        }

                        return $result;
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
