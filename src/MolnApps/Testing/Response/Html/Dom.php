<?php

namespace MolnApps\Testing\Response\Html;

class Dom
{
	private $domParser;
	private $attributes = [];

	public function __construct($markup = null, DomParser $domParser = null)
	{
		$this->domParser = ($domParser) ?: new OldDomParser;
		$this->load($markup);
	}
	
	public function load($markup)
	{
		$this->domParser->load($markup);

		return $this;
	}

	public function select($attributes)
	{
		if ($attributes instanceof Selector) {
			$this->attributes = array_merge($this->attributes, $attributes->toArray());
		} else {
			$this->attributes[] = $attributes;
		}

		return $this;
	}

	public function find($selector = null)
	{
		if ($selector) {
			$this->attributes = array_merge($this->attributes, (new Selector($selector))->toArray());
		}

		$result = $this->domParser->find($this->attributes);

		$this->reset();

		return $result;
	}

	public function findFirst($selector = null)
	{
		$collection = $this->find($selector);

		if (count($collection) > 0) {
			return $collection[0];
		}
	}

	public function reset()
	{
		$this->attributes = [];
	}
}