Silex-i18n LinkServiceProvider
==============================
## Use

### Code

##### web/index.php
```php
<?php

require_once __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../app/src/app.php';
$app->run();

?>
```

##### app/src/app.php
```php
<?php

use Silex\Application;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\UrlGeneratorServiceProvider;
use SimpleSilex\SilexI18n\Provider\LinkServiceProvider;

/**
 * Creates an application
 */
$app = new Silex\Application();

/**
 * Registers providers
 */
$app->register(new TwigServiceProvider());
$app->register(new UrlGeneratorServiceProvider());
$app->register(new LinkServiceProvider());

/**
 * Configures the application
 */
$app['system_locales'] = array(
    'en' => array(
        'abbr' => 'En',
        'name' => 'English',
        'flag' => 'english-flag',
    ),
    'fr' => array(
        'abbr' => 'Fr',
        'name' => 'Français',
        'flag' => 'french-flag',
    ),
    'uk' => array(
        'abbr' => 'Укр',
        'name' => 'Українська',
        'flag' => 'ukrainian-flag',
    ),
);
$app['locale'] = 'en';

$app['twig.path'] = __DIR__ . '/../templates';

/**
 * Defines some controllers
 */
$app->get('/{_locale}/', function (Application $app) {
    return $app['twig']->render('links.twig');
})->bind('home');

$app->get('/some/page/', function (Application $app) {
    return $app['twig']->render('links.twig');
})->bind('some-page');

$app->get('/{_locale}/page/', function (Application $app) {
    return $app['twig']->render('links.twig');
})->bind('page');

return $app;

?>
```

##### app/templates/links.twig
```html
<!DOCTYPE html>
<html lang="{{ app.locale }}">
<head>
    <meta charset="utf-8">
    <title>Test</title>
</head>
<body>
    <ul class="nav">
        <li {{- active_link('home') }}>
            <a href="{{ path('home') }}">Home</a>
        </li>
        <li {{- active_link('page') }}>
            <a href="{{ path('page') }}">Page</a>
        </li>
        <li {{- active_link('some-page', 'last-nav-item') }}>
            <a href="{{ path('some-page') }}">Some page (default locale)</a>
        </li>
    </ul>
    <ul class="lang">
        {% for locale, params in app.system_locales %}
        <li {{- active_locale(locale, params.flag) }}>
            <a href="{{ localelink_path(locale) }}" title="{{ params.name }}">
                {{ params.abbr }}
            </a>
        </li>
        {% endfor %}
    </ul>
</body>
</html>
```

### Result

##### http://example.com/fr/page/
```html
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Test</title>
</head>
<body>
    <ul class="nav">
        <li>
            <a href="/fr/">Home</a>
        </li>
        <li class="active">
            <a href="/fr/page/">Page</a>
        </li>
        <li class="last-nav-item">
            <a href="/some/page/">Some page (default locale)</a>
        </li>
    </ul>
    <ul class="lang">
        <li class="english-flag">
            <a href="/en/page/" title="English">
                En
            </a>
        </li>
        <li class="french-flag active">
            <a href="/fr/page/" title="Français">
                Fr
            </a>
        </li>
        <li class="ukrainian-flag">
            <a href="/uk/page/" title="Українська">
                Укр
            </a>
        </li>
    </ul>
</body>
</html>
```

##### http://example.com/some/page/
```html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Test</title>
</head>
<body>
    <ul class="nav">
        <li>
            <a href="/en/">Home</a>
        </li>
        <li>
            <a href="/en/page/">Page</a>
        </li>
        <li class="last-nav-item active">
            <a href="/some/page/">Some page (default locale)</a>
        </li>
    </ul>
    <ul class="lang">
        <li class="english-flag active">
            <a href="/some/page/" title="English">
                En
            </a>
        </li>
        <li class="french-flag">
            <a href="/fr/some/page/" title="Français">
                Fr
            </a>
        </li>
        <li class="ukrainian-flag">
            <a href="/uk/some/page/" title="Українська">
                Укр
            </a>
        </li>
    </ul>
</body>
</html>
```

##### http://example.com/fr/some/page/
`Status Code:404 Not Found`
