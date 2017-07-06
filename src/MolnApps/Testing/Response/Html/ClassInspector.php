<?php

namespace MolnApps\Testing\Response\Html;

class ClassInspector
{
	private $expectedClasses = [];
	private $actualClasses = [];

	public function __construct($expectedClasses, $actualClasses)
	{
		$this->expectedClasses = $this->normalizeClasses($expectedClasses);
		$this->actualClasses = $this->normalizeClasses($actualClasses);
	}

	public function contains()
	{
		foreach ($this->expectedClasses as $expectedClass) {
			if ( ! in_array($expectedClass, $this->actualClasses)) {
				return false;
			}
		}
		return true;
	}

	private function normalizeClasses($classes)
	{
		if (is_string($classes)) {
			$classes = [$classes];
		}

		$result = [];
		
		foreach ($classes as $class) {
			$result = array_merge($result, explode(' ', $class));
		}

		return $result;
	}
}