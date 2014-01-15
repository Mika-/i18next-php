<?php

class i18next {

	private $_path = null;
	private $_language = null;
	private $_translation = array();
	private static $_instance = null;


	private function __construct($language, $path) {

		$this->_language = $language;
		$this->_path = $path;

		$this->loadTranslation();

	}

	public static function getInstance($language = 'en', $path = null) {

		if (!isset(self::$_instance))
			self::$_instance = new i18next($language, $path);

		return self::$_instance;

	}

	private function loadTranslation() {

		$path = preg_replace('/__(.+?)__/', '*', $this->_path, 2, $hasNs);

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

				$regexp = preg_replace('/__(.+?)__/', '(?<$1>.+)?', preg_quote($this->_path, '/'));
				preg_match('/^' . $regexp . '$/', $file, $ns);

				if (!array_key_exists('lng', $ns))
					$ns['lng'] = $this->_language;

				if (array_key_exists('ns', $ns)) {

					if (array_key_exists($ns['lng'], $this->_translation) && array_key_exists($ns['ns'], $this->_translation[$ns['lng']]))
						$this->_translation[$ns['lng']][$ns['ns']] = array_merge($this->_translation[$ns['lng']][$ns['ns']], array($ns['ns'] => $translation));

					else if (array_key_exists($ns['lng'], $this->_translation))
						$this->_translation[$ns['lng']] = array_merge($this->_translation[$ns['lng']], array($ns['ns'] => $translation));

					else
						$this->_translation[$ns['lng']] = array($ns['ns'] => $translation);

				}
				else {

					if (array_key_exists($ns['lng'], $this->_translation))
						$this->_translation[$ns['lng']] = array_merge($this->_translation[$ns['lng']], $translation);

					else
						$this->_translation[$ns['lng']] = $translation;

				}

			}
			else {

				if (array_key_exists($this->_language, $translation))
					$this->_translation = $translation;

				else
					$this->_translation = array_merge($this->_translation, $translation);

			}

		}

	}

	public function getTranslation($key, $variables = array()) {

		$return = false;

		if (isset($variables['lng']) && isset($this->_translation[$variables['lng']]))
			$translation = $this->_translation[$variables['lng']];

		else if (isset($this->_translation[$this->_language]))
			$translation = $this->_translation[$this->_language];

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

}