<?php

namespace MolnApps\Testing\Form;

use \MolnApps\Testing\Response\Html\Node;

class FormElementFactory
{
	public static function create(Node $node)
	{
		$tag = $node->getTag();
		
		switch ($tag)
		{
			case 'textarea':
				return new Textarea($node);
			case 'select':
				return new Select($node);
			case 'input':
				if ($node->getAttribute('type') == 'checkbox') {
					return new Checkbox($node);
				}
				if ($node->getAttribute('type') == 'file') {
					return new File($node);
				}
				return new Input($node);
			default:
				throw new \Exception('Unknown form element '.$tag);
		}
	}
}