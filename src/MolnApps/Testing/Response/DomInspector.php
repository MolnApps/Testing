<?php

namespace MolnApps\Testing\Response;

use \MolnApps\Testing\Response\Html\Dom;
use \MolnApps\Testing\Response\Html\Selector;
use \MolnApps\Testing\Response\Html\SelectorBuilder;
use \MolnApps\Testing\Response\Html\SelectorEncoder;

/*
Todo:
— Test ordered() with withChild() method
— Test: we must make sure that selector parameters are not overridden by attributes
  e.g. $builder->seeElement('div.Item')->withAttribute('class', 'Report')

— Merge Abstract node back into DomNode? Or at least, refactor
— Rename OldDomParser to DomParser
*/

class DomInspector
{
	private $markup;
	private $selectorAttributes = [];

	private $collection = [];

	private $atLeast = false;
	private $atMost = false;
	private $times;
	private $ordered;
	private $index = 0;
	private $attributes;

	private $prophecy = false;
	private $shouldBeSeen = true;

	private $selectorBuilder;

	public function __construct($markup = null)
	{
		$this->selectorBuilder = new SelectorBuilder;

		$this->setResponse($markup);
	}

	public function __call($methodName, $args)
	{
		if (substr($methodName, 0, 4) == 'with' && count($args) == 1) {
			$attribute = lcfirst(substr($methodName, 4));
			return $this->withAttribute($attribute, $args[0]);
		}

		throw new \BadMethodCallException('Method ::'.$methodName.'() does not exists');
	}

	private function getSelectorBuilder()
	{
		return $this->selectorBuilder;
	}

	public function setResponse($markup)
	{
		$this->markup = $markup;

		return $this;
	}

	public function shouldSeeElement($selector = null)
	{
		return $this->prophecy()->seeElement($selector);
	}

	public function prophecy()
	{
		$this->prophecy = true;

		return $this;
	}

	public function seeElement($selector = null, $shouldBeSeen = true)
	{
		$this->reset();

		$this->shouldBeSeen = !! $shouldBeSeen;
		$this->getSelectorBuilder()->seeElement($selector);

		return $this->findIfNotProphecy();
	}

	public function dontSeeElement($selector  = null)
	{
		return $this->seeElement($selector, false);
	}

	private function reset()
	{
		$this->atLeast = false;
		$this->atMost = false;
		$this->times = null;
		$this->ordered = false;
		$this->index = 0;
		$this->getSelectorBuilder()->reset();
	}

	public function withAttributes(array $attributes)
	{
		if ($this->ordered) {
			$this->attributes = $attributes;
		} else {
			$this->getSelectorBuilder()->withAttributes($attributes);
		}

		return $this->findIfNotProphecy();
	}
	
	public function withAttribute($attribute, $value)
	{
		return $this->withAttributes([$attribute => $value]);
	}

	public function withChild($childSelector = null)
	{
		$this->getSelectorBuilder()->withChild($childSelector);

		return $this->findIfNotProphecy();
	}

	// ! Prophecy assertion methods

	public function never()
	{
		return $this->times(0);
	}

	public function once()
	{
		return $this->times(1);
	}

	public function times($count)
	{
		$this->times = $count;
		
		return $this->find();
	}

	// ! Modifiers methods

	public function atLeast()
	{
		$this->atLeast = true;

		return $this;
	}

	public function atMost()
	{
		$this->atMost = true;

		return $this;
	}

	public function ordered()
	{
		$this->ordered = true;
		$this->index = 0;

		return $this;
	}

	private function guardMarkup()
	{
		if ( ! $this->markup) {
			throw new \Exception('Please provide some markup to inspect');
		}
	}

	private function findIfNotProphecy()
	{
		return ( ! $this->prophecy) ? $this->findAndResetProphecty() : $this;
	}

	private function findAndResetProphecty()
	{
		$this->find();
		$this->prophecy = false;

		return $this;
	}

	private function find()
	{
		$selector = $this->getSelectorBuilder()->getSelector();

		foreach ($selector as $selectorAttributes) {
			$this->collection = (new Dom($this->markup))->select($selectorAttributes)->find();
			$collectionCount = count($this->collection);

			if (is_null($this->times) && $collectionCount < 1 && $this->shouldBeSeen) {
				throw new \Exception($this->getExceptionMessage($selectorAttributes));
			}

			if (is_null($this->times) && $collectionCount != 0 && ! $this->shouldBeSeen) {
				throw new \Exception($this->getExceptionMessage($selectorAttributes));
			}
			
			if ( ! is_null($this->times) && ! $this->atLeast && ! $this->atMost && $collectionCount != $this->times) {
				throw new \Exception($this->getExceptionMessage($selectorAttributes, $collectionCount));
			}

			if ( ! is_null($this->times) && $this->atLeast && $collectionCount < $this->times) {
				throw new \Exception($this->getExceptionMessage($selectorAttributes, $collectionCount));
			}

			if ( ! is_null($this->times) && $this->atMost && $collectionCount > $this->times) {
				throw new \Exception($this->getExceptionMessage($selectorAttributes, $collectionCount));
			}

			if ($this->ordered && ! $this->itemAtIndexMatches($this->collection)) {
				$attributes = array_merge($selectorAttributes, $this->attributes);
				throw new \Exception($this->getExceptionMessage($attributes));
			}
		}

		return $this;
	}

	public function getElement()
	{
		if (isset($this->collection[0])) {
			return $this->collection[0];
		}
		return null;
	}

	private function itemAtIndexMatches($collection)
	{
		if ( 
			! isset($collection[$this->index]) || 
			! $collection[$this->index]->matches($this->attributes)
		) {
			return false;
		}

		++$this->index;

		return true;
	}

	private function getExceptionMessage($attributes, $count = null)
	{
		$message = sprintf('Element %s', $this->getAttributesString($attributes));

		$message.= ' was expected';

		if ($this->ordered) {
			$message.= ' at index '.$this->index;
			return $message;
		}

		if (is_null($this->times)) {
			if ($this->shouldBeSeen) {
				$message.= ' at least 1 time';
			} else {
				$message.= ' 0 times';
			}
		} else {
			if ($this->atLeast) {
				$message.= ' at least';
			}
			if ($this->atMost) {
				$message.= ' at most';
			}
			$message.= sprintf(' %s times but was found %s times', $this->times, $count);
		}

		return $message;
	}

	private function getAttributesString($attributes)
	{
		return (new SelectorEncoder([$attributes]))->encode();
	}
}