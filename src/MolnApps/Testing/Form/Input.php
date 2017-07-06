<?php

namespace MolnApps\Testing\Form;

class Input extends AbstractFormElement
{
	public function setValue($value)
	{
		$this->domElement->setAttribute('value', $value);
	}

	public function getValue()
	{
		return $this->domElement->getAttribute('value');
	}
}