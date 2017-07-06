<?php

namespace MolnApps\Testing\Form;

use \MolnApps\Testing\Response\Html\Node;

class Select extends AbstractFormElement
{
	private $options;

	public function __construct(Node $selectElement)
	{
		parent::__construct($selectElement);
		$this->updateOptions();
	}

	private function updateOptions()
	{
		$this->options = $this->domElement->find('option');
	}

	public function setValue($value)
	{
		$this->reset();
		$this->select($value);
	}

	public function getValue()
	{
		$selectedOptions = $this->domElement->find('option[selected="selected"]');

		$values = [];

		foreach ($selectedOptions as $selectedOption) {
			$values[] = $selectedOption->getAttribute('value');
		}

		if ( $values && ! $this->isMultiple()) {
			return $values[0];
		}

		if ( ! $this->isMultiple()) {
			$firstOption = $this->domElement->findFirst('option');
			if ($firstOption) {
				return $firstOption->getAttribute('value');	
			}
			return '';
		}

		return $values;
	}

	public function hasOption($value)
	{
		return in_array($value, $this->getAvailableValues());
	}

	private function getAvailableValues()
	{
		$tmp = [];
		
		foreach ($this->options as $option) {
			$tmp[] = $option->getAttribute('value');
		}
		
		return $tmp;
	}

	private function select($value)
	{
		$values = $this->normalizeValue($value);
		
		foreach ($values as $value) {
			if ($value || $value === '0') {
				$this->findOrCreateOption($value)->setAttribute('selected', 'selected');
			}
		}
	}

	private function findOrCreateOption($value)
	{
		if ( ! $this->hasOption($value)) {
			$this->addForgedOption($value);
		}

		return $this->findOption($value);
	}

	private function findOption($value)
	{
		foreach ($this->options as $option) {
			if ($option->getAttribute('value') == $value || (! $value && ! $option->getAttribute('value'))) {
				return $option;
			}
		}
	}

	private function addForgedOption($value)
	{
		$this->domElement->addChild('option', ['value' => $value]);
		$this->updateOptions();
	}

	private function normalizeValue($value)
	{
		return (array)$value;
	}

	private function reset()
	{
		foreach ($this->options as $optionElement) {
			$optionElement->setAttribute('selected', null);
		}
	}

	private function isMultiple()
	{
		return in_array('multiple', array_keys($this->domElement->getAttributes()));
	}
}