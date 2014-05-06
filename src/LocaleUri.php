<?php

namespace SimpleSilex\SilexI18n;

class LocaleUri
{
    protected $uri;
    protected $parsedPath;
    protected $parsedLocale;

    /**
     * Constructor.
     *
     * @param string $requestUri The URI
     * @param array  $localeList The list of locales
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
     * Gets URI.
     *
     * @return string The URI
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * Gets the URI without locale.
     *
     * @return string The URI
     */
    public function getParsedPath()
    {
        return $this->parsedPath;
    }

    /**
     * Gets the locale of URI.
     *
     * @return string The locale
     */
    public function getParsedLocale()
    {
        return $this->parsedLocale;
    }
}
