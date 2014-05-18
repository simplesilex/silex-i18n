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
            <a href="{{ path('some-page') }}">Page</a>
        </li>
    </ul>
    <ul class="lang">
        {% for locale, params in app.system_locales %}
        <li {{- active_locale(locale, 'lang-item') }}>
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

##### http://example.com/en/page/
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
        <li class="active">
            <a href="/en/page/">Page</a>
        </li>
        <li class="last-nav-item">
            <a href="/some/page/">Some page</a>
        </li>
    </ul>
    <ul class="lang">
        <li class="lang-item active">
            <a href="/en/page/" title="English">
                En
            </a>
        </li>
        <li class="lang-item">
            <a href="/fr/page/" title="Français">
                Fr
            </a>
        </li>
        <li class="lang-item">
            <a href="/uk/page/" title="Українська">
                Укр
            </a>
        </li>
    </ul>
</body>
</html>
```