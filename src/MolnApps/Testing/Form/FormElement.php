<?php

namespace MolnApps\Testing\Form;

interface FormElement
{
	public function setValue($value);
	public function getValue();
	public function getName();
}