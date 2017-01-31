<?php

namespace CoderStudios\Library;

class BaseLibrary {

	protected $namespace = __NAMESPACE__;

	protected $basename;

	protected function getKeyName($string) {
		return md5(snake_case(str_replace('\\','',$this->namespace) . $this->basename . '_' . $string));
	}

}