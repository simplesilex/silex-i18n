Silex-i18n DateServiceProvider
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
use Silex\Provider\TranslationServiceProvider;
use Symfony\Component\Translation\Loader\YamlFileLoader;
use SimpleSilex\SilexI18n\Provider\DateServiceProvider;

/**
 * Creates an application
 */
$app = new Application();

/**
 * Registers providers
 */
$app->register(new TwigServiceProvider());
$app->register(new TranslationServiceProvider());
$app->register(new DateServiceProvider());

$app['translator'] = $app->share(
    $app->extend('translator', function($translator, $app) {
        $translator->addLoader('yaml', new YamlFileLoader());
        $translator->addResource('yaml', __DIR__.'/../locales/de.yml', 'de');

        return $translator;
    })
);

/**
 * Configures the application
 */
$app['system_locales'] = array(
    'en-US' => array(
        'name' => 'American',
        'date_time' => 'n/j/Y g:i:s A', // 4/17/2014 4:32:12 AM
        'short_date' => 'n/j/y',        // 4/17/14
        'medium_date' => 'M j, Y',      // Apr 17, 2014
        'long_date' => 'F j, Y',        // April 17, 2014
        'full_date' => 'l, F j, Y',     // Thursday, April 17, 2014
        'some_format' => 'D, M j, Y',   // Thu, Apr 17, 2014
    ),
    'en' => array(
        'name' => 'English',
        'date_time' => 'd/m/Y H:i:s',   // 17/04/2014 16:32:12
        'short_date' => 'd/m/y',        // 17/04/14
        'medium_date' => 'd-M-Y',       // 17-Apr-2014
        'long_date' => 'j F Y',         // 17 April 2014
        'full_date' => 'j F Y',         // 17 April 2014
        'some_format' => 'D, j M Y',    // Thu, 17 Apr 2014
    ),
    'de' => array(
        'name' => 'Deutsch',
        'date_time' => 'd.m.Y H:i:s',   // 17.04.2014 16:32:12
        'short_date' => 'd.m.y',        // 17.04.14
        'medium_date' => 'd.m.Y',       // 17.04.2014
        'long_date' => 'j. F Y',        // 17. April 2014
        'full_date' => 'l, j. F Y',     // Donnerstag, 17. April 2014
        'some_format' => 'D, j. M Y',   // Do, 17. Apr 2014
    ),
);
$app['locale'] = 'en';
$app['locale_fallbacks'] = array('en');

$app['twig.path'] = __DIR__ . '/../templates';

/**
 * Defines some controllers
 */
$app->get(
    '/{_locale}/',
    function (Application $app) {
        $datetime = new \DateTime();
        return $app['twig']->render('dates.twig', array(
            'datetime' => $datetime
        ));
    }
);

return $app;

?>
```

##### app/locales/de.yml
```yml
January: Januar
Jan: Jan
February: Februar
Feb: Feb
March: März
Mar: Mär
April: April
Apr: Apr
May: Mai
June: Juni
Jun: Jun
July: Juli
Jul: Jul
August: August
Aug: Aug
September: September
Sep: Sep
October: Oktober
Oct: Okt
November: November
Nov: Nov
December: Dezember
Dec: Dez,
Sunday: Sonntag
Sun: So
Monday: Montag
Mon: Mo
Tuesday: Dienstag
Tue: Di
Wednesday: Mittwoch
Wed: Mi
Thursday: Donnerstag
Thu: Do
Friday: Freitag
Fri: Fr
Saturday: Samstag
Sat: Sa
```

##### app/templates/dates.twig
```html
<!DOCTYPE html>
<html lang="{{ app.locale }}">
<head>
    <meta charset="utf-8">
    <title>Tests dates</title>
</head>
<body>
    <div class="date-time">{{ datetime|localedate('date_time') }}</div>
    <div class="short-date">{{ datetime|localedate('short_date') }}</div>
    <div class="medium-date">{{ datetime|localedate('medium_date') }}</div>
    <div class="long-date">{{ datetime|localedate('long_date') }}</div>
    <div class="full-date">{{ datetime|localedate('full_date') }}</div>
    <div class="some-format">{{ datetime|localedate('some_format') }}</div>
</body>
</html>
```

### Result

##### http://example.com/de/
```html
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <title>Tests dates</title>
</head>
<body>
    <div class="date-time">17.05.2014 02:04:12</div>
    <div class="short-date">17.05.14</div>
    <div class="medium-date">17.05.2014</div>
    <div class="long-date">17. Mai 2014</div>
    <div class="full-date">Samstag, 17. Mai 2014</div>
    <div class="some-format">Sa, 17. Mai 2014</div>
</body>
</html>
```
