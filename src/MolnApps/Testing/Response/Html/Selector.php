<?php

namespace MolnApps\Testing\Response\Html;

class Selector
{
	private $selector;

	public function __construct($selector)
	{
		$this->selector = (is_array($selector)) 
			 ? (new SelectorEncoder($selector))->encode()
			 : $selector;
	}

	public function toArray()
	{
		return (new SelectorDecoder($this->selector))->decode();
	}

	public function toString()
	{
		return (new SelectorEncoder($this->toArray()))->encode();
	}
}