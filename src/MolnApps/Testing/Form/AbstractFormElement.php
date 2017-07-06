<?php

namespace MolnApps\Testing\Form;

use \MolnApps\Testing\Response\Html\Node;

abstract class AbstractFormElement implements FormElement
{
	protected $domElement;

	public function __construct(Node $domElement)
	{
		$this->domElement = $domElement;
	}

	public function getName()
	{
		return $this->domElement->getAttribute('name');
	}
}