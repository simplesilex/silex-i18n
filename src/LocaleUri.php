<?php

namespace SimpleSilex\SilexI18n;

class LocaleUri
{
    protected $uri;

    /**
     * Constructor.
     *
     * @param string $requestUri The URI
     */
    public function __construct($requestUri)
    {
        $this->uri = $requestUri;
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
}
