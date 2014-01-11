i18next-php
===================
PHP class for basic [i18next](https://github.com/jamuhl/i18next) functionality.

## Features

- Support for variables
- Support for basic plural forms
- Support for basic sprintf

## Usage

```php
// get i18next instance
$i18next = i18next::getInstance('en');

// get translation by key
echo $i18next->getTranslation('common.dog');
```

## Methods

### i18next::getInstance( string $languageKey [, string $path = null ] );
Loads `translation.json` from given path and returns i18next instance.
```php
$i18next = i18next::getInstance('en', 'my/path/');
```

### string getTranslation( string $key [, array $variables = array() ] );
Returns translated string by key.
```php
$i18next->getTranslation('common.cat', array('count' => 2, 'lng' => 'fi'));
```