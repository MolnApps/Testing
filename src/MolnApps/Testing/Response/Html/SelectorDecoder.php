<?php

namespace MolnApps\Testing\Response\Html;

class SelectorDecoder
{
	private $selector;

	public function __construct($selectorString)
	{
		$this->selector = $selectorString;
	}

	public function decode()
	{
		return $this->parseSelectors($this->selector);
	}

	private function parseSelectors($selector)
	{
		if ($selector == '*') {
			$selector = '';
		}

		$selectors = array_map('trim', explode(',', $selector));

		return array_map([$this, 'parseSelector'], $selectors);
	}

	private function parseSelector($selector)
	{
		$children = $this->splitSelectorIntoChildren($selector);

		$result = array_map([$this, 'parseSelectorChild'], $children);

		return $this->nestChildren($result);
	}

	private function nestChildren($childSelectors)
	{
		for ($i = count($childSelectors) - 1; $i >= 0; $i--) {
			$childSelectors[$i - 1]['children'] = $childSelectors[$i];
		}

		return $childSelectors[0];
	}

	private function splitSelectorIntoChildren($selector)
	{
		$children = explode(' ', $selector);

		$result = [];
		
		// Failed at regex, I'm resorting to this.
		// If a space within quotes was split, join the parts back together
		for ($i = 0; $i < count($children); $i++) {
			if (stripos($children[$i], '"') !== false) {
				$str = $children[$i];
				while (isset($children[$i + 1])) {
					$str.= ' ' . $children[$i + 1];
					$i++;
					if (stripos($children[$i], '"') !== false) {
						// If there are no two concatenated [text="..."]
						if (stripos($children[$i], '][') === false) {
							break;
						}
					}
				}
				$result[] = $str;
			} else {
				$result[] = $children[$i];
			}
		}

		return $result;
	}

	private function parseSelectorChild($selector)
	{
		$pattern = '/^(?P<tag>[\*|\w|\-]+)?(?P<id>#[\w|\-]+)?(?P<class>\.[\w|\-|\.]+)*(?P<data>\[.+\])*$/';

		preg_match($pattern, $selector, $matches);

		$result = [];

		$keys = ['tag', 'class', 'id'];

		foreach ($keys as $key) {
			$result[$key] = $this->normalizeKey($key, $matches);
		}

		$result = array_merge($result, (array)$this->normalizeKey('data', $matches));

		return array_filter($result);
	}

	private function normalizeKey($key, $matches)
	{
		if ( ! isset($matches[$key])) {
			return;
		}

		$methodName = 'normalize'.ucfirst($key);

		if (method_exists($this, $methodName)) {
			return $this->$methodName($matches[$key]);
		}

		return $matches[$key];
	}

	private function normalizeTag($tag)
	{
		return ($tag != '*') ? $tag : null;
	}

	private function normalizeClass($classes)
	{
		$array = array_values(array_filter(explode('.', $classes)));
		
		return $array;
	}

	private function normalizeId($id)
	{
		return str_replace('#', '', $id);
	}

	private function normalizeData($data)
	{
		$data = $this->convertDataToQueryString($data);

		parse_str($data, $result);

		return $result;
	}

	private function convertDataToQueryString($data)
	{
		$data = $this->convertDataToArray($data);

		$data = $this->convertKeyDoublesToArray($data);
		
		return implode('&', $data);
	}

	private function convertKeyDoublesToArray($data)
	{
		$groupedData = $this->groupDataByKey($data);

		$dataWithArrayKeys = [];
		foreach ($groupedData as $key => $values) {
			if (count($values) > 1) {
				foreach ($values as $v) {
					$dataWithArrayKeys[] = $key.'[]='.$v;
				}
			} else {
				$dataWithArrayKeys[] = $key.'='.$values[0];
			}
		}
		return $dataWithArrayKeys;
	}

	private function groupDataByKey($data)
	{
		$grouped = [];
		
		foreach ($data as $row) {
			list ($key, $value) = explode('=', $row);
			$grouped[$key][] = $value;
		}
		
		return $grouped;
	}

	private function convertDataToArray($data)
	{
		$data = explode('"][', $data);
		
		// Reintroduce double quotes at the end of each item
		foreach ($data as $i => $d) {
			if ($i != count($data) - 1) {
				$data[$i] = $d . '"';
			}
		}
		
		$data = $this->removeSquareBrackets($data);
		
		$data = $this->removeQuotes($data);
		
		return $data;
	}

	private function removeSquareBrackets($data)
	{
		$result = [];
		
		foreach ($data as $piece) {
			$removeSquareBrackets = '/^\[|\]$/';
			$result[] = preg_replace($removeSquareBrackets, '', $piece);
		}

		return $result;
	}

	private function removeQuotes($result)
	{
		$result2 = [];
		
		foreach ($result as $i => $param) {
			$result2[$i] = str_replace('"', '', $param);
		}

		return $result2;
	}
}