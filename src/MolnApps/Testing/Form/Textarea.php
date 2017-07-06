<?php

namespace MolnApps\Testing\Form;

class Textarea extends AbstractFormElement
{
	public function setValue($value)
	{
		$this->domElement->setAttribute('text', $value);
	}

	public function getValue()
	{
		return $this->domElement->getTextWithNl();
	}
}