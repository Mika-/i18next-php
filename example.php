<?php

require_once('i18next.php');

try {

	i18next::init('en');

} catch (Exception $e) {

	echo 'Caught exception: ' . $e->getMessage();

}

function t($key, $variables = array()) {

	return i18next::getTranslation($key, $variables);

}

echo 'common.dog -> ' . t('common.dog');

echo '<br>';

echo 'common.cat { count: 1 } -> ' . t('common.cat', array('count' => 1));

echo '<br>';

echo 'common.cat { count: 2 } -> ' . t('common.cat', array('count' => 2));

echo '<br>';

echo 'common.cat { count: 2, lng: fi } -> ' . t('common.cat', array('count' => 2, 'lng' => 'fi'));

echo '<hr>Array: common.thedoglovers -><br>';

echo implode("<br>",t('common.thedoglovers'));

?>
