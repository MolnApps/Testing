<?php

namespace MolnApps\Testing\Response\Html;

class SelectorEncoder
{
	private $selector;

	public function __construct(array $selector)
	{
		$this->selector = $selector;
	}

	public function encode()
	{
		return $this->encodeSelectors($this->selector);
	}

	private function encodeSelectors($selectors, $baseStr = '')
	{
		$encodedSelectors = array_map([$this, 'encodeSelector'], $selectors);
		return implode(', ', $encodedSelectors);
	}

	private function encodeSelector($arraySelector, $stringSelector = '')
	{
		$stringSelector.= $this->addTag($arraySelector);
		$stringSelector.= $this->addId($arraySelector);
		$stringSelector.= $this->addClasses($arraySelector);
		$stringSelector.= $this->addData($arraySelector);
		
		$stringSelector = $this->encodeChildren($arraySelector, $stringSelector);
		
		return $stringSelector;
	}

	private function addTag($arraySelector)
	{
		if (isset($arraySelector['tag'])) {
			return $arraySelector['tag'];
		}
	}

	private function addId($arraySelector)
	{
		if (isset($arraySelector['id'])) {
			return '#' . $arraySelector['id'];
		}
	}

	private function addClasses($arraySelector)
	{
		if (isset($arraySelector['class'])) {
			$classes = $this->normalizeClasses($arraySelector['class']);
			return '.' . implode('.', $classes);
		}
	}

	private function normalizeClasses($classes)
	{
		$tmp = [];
		
		foreach ((array)$classes as $class) {
			$tmp = array_merge($tmp, explode(' ', $class));
		}
		
		return $tmp;
	}

	private function addData($arraySelector)
	{
		$arraySelector = array_filter($arraySelector, [$this, 'isDataKey'], ARRAY_FILTER_USE_KEY);
		array_walk($arraySelector, [$this, 'addDataKey']);
		return implode('', $arraySelector);
	}

	private function addDataKey(&$value, $key)
	{
		$dataKeys = [];
		
		foreach ((array) $value as $v) {
			$dataKeys[] = '['.$key.'="'.$v.'"]';
		}
		
		$value = implode('', $dataKeys);
	}

	private function isDataKey($key)
	{
		$specialKeys = ['tag', 'class', 'id', 'children'];
		return ! in_array($key, $specialKeys);
	}

	private function encodeChildren($arraySelector, $parentStringSelector)
	{
		if (isset($arraySelector['children'])) {
			$tmp = [];
			foreach ($arraySelector['children'] as $child) {
				$tmp[] = $this->encodeSelector($child, $parentStringSelector.' ');
			}
			$parentStringSelector = implode(', ', $tmp);
		}
		return $parentStringSelector;
	}
}