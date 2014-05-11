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
                'short_date' => 'm/d/y', // 17/04/14
                'medium_date' => 'd M. Y', // 17 Apr. 2014
                'long_date' => 'j F Y', // 17 Avril 2014
                'full_date' => 'l j F Y', // Jeudi 17 Avril 2014
            ),
        );
        $app['locale'] = 'en';
    }

    /**
     * {@inheritDoc}
     */
    public function boot(Application $app)
    {
    }
}
