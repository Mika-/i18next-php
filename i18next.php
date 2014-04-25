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
        /**
         * Fall back language for translations not found in current language
         * @var string fall back language
         */
        private static $_fallBackLanguage = 'dev';
        
        /**
         * Inits i18next static class...
         * 
         * path may include __lng___ and __ns__ placeholders so all languages and namespaces are loaded!
         * 
         * @param string $language locale language code
         * @param string $path path to locale json files
         */
	public static function init($language = 'en', $path = null) {

		self::$_language = $language;
		self::$_path = $path;

		self::loadTranslation();

	}

        /**
         * Change default language and fall back language
         * If fallback is not set it is left unchanged
         * 
         * @param string $language New default language
         * @param string $fallback Fallback language
         */
	public static function setLanguage($language,$fallback=null) {
		self::$_language = $language;
		if (!empty($fallback)) {
                    self::$_fallBackLanguage = $fallback;
                }
	}

	public static function existTranslation($key) {

		$return = self::_getKey($key);

		if ($return)
			$return = true;

		return $return;

	}

	public static function getTranslation($key, $variables = array()) {

		$return = self::_getKey($key, $variables);

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

        /**
         * Loads translation(s)
         * @throws Exception
         */
	private static function loadTranslation() {

		$path = preg_replace('/__(.+?)__/', '*', self::$_path, 2, $hasNs);

		if (!preg_match('/\.json$/', $path)) {
			$path = $path . 'translation.json';
                        // Fix for ns & lng parser to work since it references self::$_path 
                        self::$_path = self::$_path . 'translation.json';
                }

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

        /**
         * Get translation for given key 
         * 
         * Translation is looked up in language specified in $variables['lng'],current language or fall back language - in this order. 
         * Fall back language is used only if defined and no explicit language was specified in $variables
         * 
         * @param string $key key for translation
         * @param array $variables variables
         * @return mixed translated string or false if no matching translation has been found
         */
	private static function _getKey($key, $variables = array()) {

		$return = false;

		if (array_key_exists('lng', $variables) && array_key_exists($variables['lng'], self::$_translation))
			$translation = self::$_translation[$variables['lng']];

		else if (array_key_exists(self::$_language, self::$_translation))
			$translation = self::$_translation[self::$_language];

		else
			$translation = array();

		foreach (explode('.', $key) as $path) {

			if (array_key_exists($path, $translation) && is_array($translation[$path])) {

				$translation = $translation[$path];

			}
			else if (array_key_exists($path, $translation)) {

				if (array_key_exists('count', $variables) && $variables['count'] != 1 && array_key_exists($path . '_plural_' . $variables['count'], $translation))
					$return = $translation[$path . '_plural' . $variables['count']];

				else if (array_key_exists('count', $variables) && $variables['count'] != 1 && array_key_exists($path . '_plural', $translation))
					$return = $translation[$path . '_plural'];

				else
					$return = $translation[$path];

				break;

			}

		}

		if (is_array($translation) && isset($variables['returnObjectTrees']) && $variables['returnObjectTrees'] === true)
			$return = $translation;
                // Fallback language check...
                if ($return === false and empty($variables['lng']) and !empty(self::$_fallBackLanguage)) {
                    $return = self::_getKey($key, array_merge($variables,array('lng'=>  self::$_fallBackLanguage)));
                }
		return $return;

	}

}