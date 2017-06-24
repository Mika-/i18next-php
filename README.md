i18next-php
===================
PHP class for basic [i18next](https://github.com/jamuhl/i18next) functionality.

## Features

- Support for [variables](http://i18next.com/pages/doc_features.html#interpolation)
- Support for [context](http://i18next.com/pages/doc_features.html#context)
- Support for [basic sprintf](http://i18next.com/pages/doc_features.html#sprintf)
- Support for [basic plural forms](http://i18next.com/pages/doc_features.html#plurals)
- Support for [multiline in JSON](http://i18next.com/pages/doc_features.html)

## Usage

```php
// init i18next instance
i18next::init('en');

// get translation by key
echo i18next::getTranslation('animal.dog');
```

## Methods

### i18next::init( string $languageKey [, string $path ] );
Loads translation files from given path. Looks for `translation.json` by default.
```php
i18next::init('en', 'my/path/');
// loads my/path/translation.json
```
You can also use variables and split namespaces and languages to different files.
```php
i18next::init('en', 'languages/__lng__/__ns__.json');
// loads languages/en/animal.json, languages/fi/animal.json, etc...
```

Method throws an exception if no files are found or the json can not be parsed.

### mixed i18next::getTranslation( string $key [, array $variables ] );
Returns translated string by key.
```php
i18next::getTranslation('animal.catWithCount', array('count' => 2, 'lng' => 'fi'));
```

### boolean i18next::existTranslation( string $key );
Checks if translated string exists.

### void i18next::setLanguage( string $language [, string $fallback ] );
Changes language.

### array i18next::getMissingTranslations();
Gets an array of missing translations.
```
array(1) {
    [0]=> array(2) {
        ["language"]=> string(2) "en"
        ["key"]=> string(14) "animal.unknown"
    }
}
```

## Multilines in JSON-arrays
You can have html content written with multilines in JSON File
```
{
	"en": {
		"common": {
			"thedoglovers": [
                "The Dog Lovers by Spike Milligan",
                "So they bought you",
                "And kept you in a",
                "Very good home"
            ]
        }
	}
}
```
