<?php

namespace MolnApps\Testing\Response\Html;

class SelectorBuilder
{
	private $selectorAttributes = [];
	private $childSelectorAttributes = [];

	private $currentSelector;
	private $currentChildSelector;

	public function seeElement($selector = null)
	{
		$selector = $this->currentSelector($selector);

		$this->resetChildrenSelector();

		$selector = new Selector($selector);
		
		$this->selectorAttributes[] = $selector->toArray();
		
		return $this;
	}

	private function currentSelector($selector)
	{
		if ($selector) {
			$this->currentSelector = $selector;
		}

		return $this->currentSelector;
	}

	public function withChild($childSelector = null)
	{
		$childSelector = $this->currentChildSelector($childSelector);

		$childSelector = new Selector($childSelector);
		
		$this->childSelectorAttributes[] = $childSelector->toArray();

		return $this;
	}

	private function currentChildSelector($selector)
	{
		if ($selector) {
			$this->currentChildSelector = $selector;
		}

		return $this->currentChildSelector;
	}

	public function withAttribute($attribute, $value)
	{
		return $this->withAttributes([$attribute => $value]);
	}

	public function withAttributes(array $attributes)
	{
		if ($this->childSelectorAttributes) {
			$this->setLastChildSelectorAttributes($this->mergeWithChildSelector($attributes));
		} else {
			$this->setLastSelectorAttributes($this->mergeWithSelector($attributes));
		}

		return $this;
	}

	public function getSelector()
	{
		$this->bindChildrenWithLastSelector();

		return $this->flattenSelectorAttributes($this->selectorAttributes);
	}

	public function reset()
	{
		$this->resetChildrenSelector();
		$this->resetSelector();
		
		return $this;
	}

	private function resetChildrenSelector()
	{
		$this->bindChildrenWithLastSelector();
		$this->childSelectorAttributes = [];
	}

	private function resetSelector()
	{
		$this->selectorAttributes = [];
	}

	private function bindChildrenWithLastSelector()
	{
		if ( ! $this->childSelectorAttributes) {
			return;
		}

		$children = $this->flattenSelectorAttributes($this->childSelectorAttributes);
		$attributes = $this->mergeWithSelector(['children' => $children]);
		$this->setLastSelectorAttributes($attributes);
	}

	private function flattenSelectorAttributes($array)
	{
		$result = [];

		foreach ($array as $selectorAttributes) {
			foreach ($selectorAttributes as $selectorAttribute) {
				$result[] = $selectorAttribute;
			}
		}

		return $result;
	}

	private function mergeWithSelector($attributes)
	{
		return $this->mergeWithCurrentSelector($this->getLastSelectorAttributes(), $attributes);
	}

	private function mergeWithChildSelector($attributes)
	{
		return $this->mergeWithCurrentSelector($this->getLastChildSelectorAttributes(), $attributes);
	}

	private function mergeWithCurrentSelector($attributes1, $attributes2)
	{
		$attributes2 = $this->normalizeAttributes($attributes2);

		foreach ($attributes1 as $i => $childSelectorAttributes) {
			$attributes1[$i] = array_merge($attributes1[$i], $attributes2);
		} 

		return $attributes1;
	}

	private function normalizeAttributes($attributes)
	{
		if (isset($attributes['class']) && ! $attributes['class']) {
			$attributes['class'] = null;
		}
		
		if (isset($attributes['class'])) {
			$newAttributes = (new Selector([$attributes]))->toArray()[0];
			$attributes['class'] = $newAttributes['class'];
		}
		
		return $attributes;
	}

	private function setLastChildSelectorAttributes($selectorArray)
	{
		$lastIndex = count($this->childSelectorAttributes) - 1;
		$this->childSelectorAttributes[$lastIndex] = $selectorArray;
	}

	private function setLastSelectorAttributes($selectorArray)
	{
		$lastIndex = count($this->selectorAttributes) - 1;
		$this->selectorAttributes[$lastIndex] = $selectorArray;
	}

	private function getLastChildSelectorAttributes()
	{
		$lastIndex = count($this->childSelectorAttributes) - 1;
		return $this->childSelectorAttributes[$lastIndex];
	}

	private function getLastSelectorAttributes()
	{
		$lastIndex = count($this->selectorAttributes) - 1;
		return $this->selectorAttributes[$lastIndex];
	}
}