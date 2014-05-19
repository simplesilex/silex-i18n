<?php

/**
 * Part of the Silex-i18n package.
 *
 * @package   Silex-i18n
 * @copyright 2014 Yuriy Davletshin
 * @license   http://opensource.org/licenses/mit-license/ The MIT License (MIT)
 * @link      http://www.simplesilex.com/
 */
namespace SimpleSilex\SilexI18n;

/**
 * Parses request URI.
 *
 * @author Yuriy Davletshin <yuriy.davletshin@gmail.com>
 */
class LocaleUri
{
    protected $uri;
    protected $clippedPath;
    protected $locale;

    /**
     * Constructor.
     *
     * @param string $requestUri An URI
     * @param array  $localeList A list of locales
     */
    public function __construct($requestUri, array $localeList)
    {
        $this->uri = $requestUri;

        $locales = implode('|', array_keys($localeList));
        $patterns = array(
            '/^\/(' . $locales . ')\//',
            '/^\/(' . $locales . ')$/'
        );
        $this->clippedPath = preg_replace($patterns, array('/', ''), $this->uri);
        $this->locale = ltrim(
            str_replace($this->clippedPath, '', $this->uri),
            '/'
        );
    }

    /**
     * Gets the URI.
     *
     * @return string The URI
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * Gets the URI-snippet without locale.
     *
     * @return string The snippet of the URI
     */
    public function getClippedPath()
    {
        return $this->clippedPath;
    }

    /**
     * Gets the locale of the URI.
     *
     * @return string The locale
     */
    public function getLocale()
    {
        return $this->locale;
    }
}
