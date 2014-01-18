<?php

/*
 * ----------------------------------------------------------------------------
 * "THE BEER-WARE LICENSE" (Revision 42):
 * <github.com/Mika-> wrote this file. As long as you retain this notice you
 * can do whatever you want with this stuff. If we meet some day, and you think
 * this stuff is worth it, you can buy me a beer in return
 * ----------------------------------------------------------------------------
 */

class i18next {

	private static $_path = null;
	private static $_language = null;
	private static $_translation = array();


	public static function init($language = 'en', $path = null) {

		self::$_language = $language;
		self::$_path = $path;

		self::loadTranslation();

	}

	public static function getTranslation($key, $variables = array()) {

		$return = false;

		if (isset($variables['lng']) && isset(self::$_translation[$variables['lng']]))
			$translation = self::$_translation[$variables['lng']];

		else if (isset(self::$_translation[self::$_language]))
			$translation = self::$_translation[self::$_language];

		else
			$translation = array();

		foreach (explode('.', $key) as $path) {

			if (isset($translation[$path]) && is_array($translation[$path])) {

				$translation = $translation[$path];

			}
			else if (isset($translation[$path])) {

				if (isset($variables['count']) && $variables['count'] != 1 && isset($translation[$path . '_plural_' . $variables['count']]))
					$return = $translation[$path . '_plural' . $variables['count']];

				else if (isset($variables['count']) && $variables['count'] != 1 && isset($translation[$path . '_plural']))
					$return = $translation[$path . '_plural'];

				else
					$return = $translation[$path];

				break;

			}

		}

		if ($return && isset($variables['postProcess']) && $variables['postProcess'] === 'sprintf' && isset($variables['sprintf'])) {

			if (is_array($variables['sprintf']))
				$return = vsprintf($return, $variables['sprintf']);

			else
				$return = sprintf($return, $variables['sprintf']);

		}

		if (!$return)
			$return = $key;

		foreach ($variables as $variable => $value) {

			if (is_string($value) || is_numeric($value))
				$return = preg_replace('/__' . $variable . '__/', $value, $return);

		}

		return $return;

	}

	private static function loadTranslation() {

		$path = preg_replace('/__(.+?)__/', '*', self::$_path, 2, $hasNs);

		if (!preg_match('/\.json$/', $path))
			$path = $path . 'translation.json';

		$dir = glob($path);

		if (count($dir) === 0)
			throw new Exception('Translation file not found');

		foreach ($dir as $file) {

			$translation = file_get_contents($file);

			$translation = json_decode($translation, true);

			if (!$translation)
				throw new Exception('Invalid json');

			if ($hasNs) {

				$regexp = preg_replace('/__(.+?)__/', '(?<$1>.+)?', preg_quote(self::$_path, '/'));
				preg_match('/^' . $regexp . '$/', $file, $ns);

				if (!array_key_exists('lng', $ns))
					$ns['lng'] = self::$_language;

				if (array_key_exists('ns', $ns)) {

					if (array_key_exists($ns['lng'], self::$_translation) && array_key_exists($ns['ns'], self::$_translation[$ns['lng']]))
						self::$_translation[$ns['lng']][$ns['ns']] = array_merge(self::$_translation[$ns['lng']][$ns['ns']], array($ns['ns'] => $translation));

					else if (array_key_exists($ns['lng'], self::$_translation))
						self::$_translation[$ns['lng']] = array_merge(self::$_translation[$ns['lng']], array($ns['ns'] => $translation));

					else
						self::$_translation[$ns['lng']] = array($ns['ns'] => $translation);

				}
				else {

					if (array_key_exists($ns['lng'], self::$_translation))
						self::$_translation[$ns['lng']] = array_merge(self::$_translation[$ns['lng']], $translation);

					else
						self::$_translation[$ns['lng']] = $translation;

				}

			}
			else {

				if (array_key_exists(self::$_language, $translation))
					self::$_translation = $translation;

				else
					self::$_translation = array_merge(self::$_translation, $translation);

			}

		}

	}

}