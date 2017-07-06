<?php

namespace MolnApps\Testing\Form;

class InputName
{
	private $name;

	public function __construct($inputName)
	{
		$this->name = $inputName;
	}

	public static function make($inputName)
	{
		return new static($inputName);
	}

	public function getName()
	{
		return $this->name;
	}

	public function getQualifiedName()
	{
		return preg_replace('/\[[a-zA-Z0-9]+\]|\[\]/', '', $this->getName());
	}

	public function getQualifiedKey()
	{
		preg_match_all('/\[([a-zA-Z0-9]+)\]/', $this->getName(), $matches);

		if (isset($matches[1]) && $matches[1]) {
			if (count($matches[1]) == 1) {
				return $matches[1][0];
			} else {
				return $matches[1];
			}
		}

		return null;
	}

	public function isArray()
	{
		return (stripos($this->getName(), '[') !== false);
	}
}