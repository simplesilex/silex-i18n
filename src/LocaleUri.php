<?php

/**
 * Part of the Silex-i18n package.
 *
 * @package   SilexLocalizer
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
    protected $parsedPath;
    protected $parsedLocale;

    /**
     * Constructor.
     *
     * @param string $requestUri An URI
     * @param array  $localeList A list of locales
     */
    public function __construct($requestUri, array $localeList)
    {
        $this->uri = $requestUri;

        $locales = '';
        foreach ($localeList as $locale => $params) {
            $locales .= $locale . '|';
        }
        $locales = rtrim($locales, '|');
        $patterns = array(
            '/^\/(' . $locales . ')\//',
            '/^\/(' . $locales . ')$/'
        );
        $this->parsedPath = preg_replace($patterns, array('/', ''), $this->uri);
        $this->parsedLocale = ltrim(
            str_replace($this->parsedPath, '', $this->uri),
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
    public function getParsedPath()
    {
        return $this->parsedPath;
    }

    /**
     * Gets the locale of the URI.
     *
     * @return string The locale
     */
    public function getParsedLocale()
    {
        return $this->parsedLocale;
    }
}
