<?php

class i18next {

	private $_path = null;
	private $_language = null;
	private $_translation = array();


	public function __construct($language = 'en', $path = null) {

		$this->_language = $language;
		$this->_path = $path;

		$this->loadTranslation();

	}

	private function loadTranslation() {

		$translation = file_get_contents($this->_path . 'translation.json');

		if (!$translation)
			return false;

		$translation = json_decode($translation, true);

		if (!$translation)
			return false;

		$this->_translation = array_merge($this->_translation, $translation);

		return true;

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

		if (!$return && isset($variables['count']))
			$return = $key . ' (__count__)';

		else if (!$return)
			$return = $key;

		foreach ($variables as $variable => $value)
			$return = preg_replace('/__' . $variable . '__/', $value, $return);

		return $return;

	}

}