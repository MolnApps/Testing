<?php

namespace MolnApps\Testing\Form;

class Checkbox extends AbstractFormElement
{
	public function setValue($value)
	{
		$value = $value ? 'checked' : null;

		$this->domElement->setAttribute('checked', $value);
	}

	public function getValue()
	{
		return ($this->domElement->getAttribute('checked')) ? $this->domElement->getAttribute('value') : '';
	}
}