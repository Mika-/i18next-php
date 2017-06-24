<?php

require __DIR__ . '/../vendor/autoload.php';

use Aiken\i18next\i18next;

try {

    i18next::init('en');

}
catch (Exception $e) {

    echo 'Caught exception: ' . $e->getMessage();

}

function t($key, $variables = array()) {

    return i18next::getTranslation($key, $variables);

}

echo 'animal.dog -> ' . t('animal.dog');

echo '<br>';

echo 'animal.catWithCount { count: 1 } -> ' . t('animal.catWithCount', array('count' => 1));

echo '<br>';

echo 'animal.catWithCount { count: 2 } -> ' . t('animal.catWithCount', array('count' => 2));

echo '<br>';

echo 'animal.catWithCount { count: 2, lng: fi } -> ' . t('animal.catWithCount', array('count' => 2, 'lng' => 'fi'));

echo '<hr>Array: animal.thedoglovers -><br>';

echo str_replace("\n", '<br>', t('animal.thedoglovers'));
