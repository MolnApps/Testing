<?php

namespace MolnApps\Testing\Response;

use \MolnApps\Testing\Response\Html\Node;

use \MolnApps\Testing\Form\FormElementFactory;

class FormInspector
{
	private $formElement;
	private $currentElementName;

	public function __construct(Node $formElement)
	{
		$this->formElement = $formElement;
	}

	// ! Fields methods

	public function seeArrayField($name, $index)
	{
		return $this->seeField($name, $index);
	}

	public function seeField($name, $index = null)
	{
		foreach (['input', 'select', 'textarea'] as $element) {
			if ($this->seeElement($element, $name, $index)) {
				return $this;
			}	
		}
		
		throw new \Exception('Could not find field ['.$this->currentElementName.']');
	}

	public function dontSeeArrayField($name, $index)
	{
		return $this->dontSeeField($name, $index);
	}

	public function dontSeeField($name, $index = null)
	{
		foreach (['input', 'select', 'textarea'] as $element) {
			$this->dontSeeElementOrFail($element, $name, $index);	
		}

		return $this;
	}

	public function seeInput($inputName)
	{
		return $this->seeElementOrFail('input', $inputName);
	}

	public function dontSeeInput($inputName)
	{
		return $this->dontSeeElementOrFail('input', $inputName);
	}

	public function seeArrayInput($inputName, $index)
	{
		return $this->seeElementOrFail('input', $inputName, $index);
	}

	public function dontSeeArrayInput($inputName, $index)
	{
		return $this->dontSeeElementOrFail('input', $inputName, $index);
	}

	public function seeTextarea($inputName)
	{
		return $this->seeElementOrFail('textarea', $inputName);
	}

	public function dontSeeTextarea($inputName)
	{
		return $this->dontSeeElementOrFail('textarea', $inputName);
	}

	public function seeSelect($inputName)
	{
		return $this->seeElementOrFail('select', $inputName);
	}

	public function dontSeeSelect($inputName)
	{
		return $this->dontSeeElementOrFail('select', $inputName);
	}

	public function seeCheckbox($inputName)
	{
		return $this->seeElementOrFail('input[type="checkbox"]', $inputName);
	}

	public function dontSeeCheckbox($inputName)
	{
		return $this->dontSeeElementOrFail('input[type="checkbox"]', $inputName);
	}

	public function seeFile($inputName)
	{
		return $this->seeElementOrFail('input[type="file"]', $inputName);
	}

	public function dontSeeFile($inputName)
	{
		return $this->dontSeeElementOrFail('input[type="file"]', $inputName);
	}

	// ! Value methods

	public function withValue($expectedValue)
	{
		$input = FormElementFactory::create($this->getCurrentElement());
		$inputValue = $input->getValue();
		
		if (is_array($inputValue)) {
			return $this->withArrayValues($inputValue, (array)$expectedValue);
		} else {
			return $this->withStringValue($inputValue, $expectedValue);
		}
	}

	private function withStringValue($inputValue, $expectedValue)
	{
		if ($inputValue != $expectedValue) {
			throw new \Exception(sprintf(
				'Could not assert that value [%s] equals to [%s]',
				$this->valueToString($expectedValue),
				$this->valueToString($inputValue)
			));
		}
	}

	private function withArrayValues(array $inputValues, array $expectedValues)
	{
		foreach ($expectedValues as $expectedValue) {
			if ( ! in_array($expectedValue, $inputValues)) {
				throw new \Exception(sprintf(
					'Could not assert that value [%s] was found in [%s]',
					$this->valueToString($expectedValue),
					$this->valueToString($inputValues)
				));
			}
		}
	}

	public function withOptions($options)
	{
		return $this->withOrWithoutOptions($options, true, 'Could not find any option with value [%s]');
	}

	public function withoutOptions($options)
	{
		return $this->withOrWithoutOptions($options, false, 'An option with value [%s] was found');
	}

	private function withOrWithoutOptions($options, $shouldBeFound, $message)
	{
		$select = FormElementFactory::create($this->getCurrentElement());
		
		foreach ($options as $option) {
			if ($select->hasOption($option) != $shouldBeFound) {
				throw new \Exception(sprintf($message, $this->valueToString($option)));
			}
		}
	}

	public function choose($value)
	{
		return $this->enter($value);
	}

	public function check()
	{
		return $this->enter(true);
	}

	public function uncheck()
	{
		return $this->enter(false);
	}

	public function enter($value)
	{
		$input = FormElementFactory::create($this->getCurrentElement());
		
		$input->setValue($value);

		return $this;
	}

	// !Implementation methods

	private function seeElementOrFail($tag, $inputName, $index = null)
	{
		$element = $this->seeElement($tag, $inputName, $index);

		if ( ! $element) {
			throw new \Exception('Could not find '.$this->currentElementName);
		}

		\PHPUnit\Framework\Assert::assertTrue(true);

		return $this;
	}

	private function dontSeeElementOrFail($tag, $inputName, $index = null)
	{
		$element = $this->seeElement($tag, $inputName, $index);

		if ($element) {
			throw new \Exception('Element '.$this->currentElementName.' was found.');
		}

		\PHPUnit\Framework\Assert::assertTrue(true);

		return $this;
	}

	private function seeElement($tag, $inputName, $index = null)
	{
		$this->setCurrentElementName($tag, $inputName, $index);

		return $this->getCurrentElement();
	}

	private function setCurrentElementName($tag, $id, $index = null)
	{
		if ( ! is_null($index)) {
			$this->currentElementName = sprintf('%s[name="%s[%s]"]', $tag, $id, $index);
		} else {
			$this->currentElementName = sprintf('%s[name="%s"]', $tag, $id);
		}
	}

	private function getCurrentElement()
	{
		if ( ! isset($this->elements[$this->currentElementName])) {
			$this->elements[$this->currentElementName] = $this->findCurrentElement();
		}

		return $this->elements[$this->currentElementName];
	}

	private function findCurrentElement()
	{
		$collection = $this->getFormElement()->find($this->currentElementName);

		if (count($collection) < 1) {
			return null;
		}

		return $collection[0];
	}

	private function getFormElement()
	{
		return $this->formElement;
	}

	private function valueToString($value)
	{
		if (is_array($value)) {
			return implode(', ', $value);
		}

		return $value;
	}
}