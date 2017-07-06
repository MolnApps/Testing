<?php

namespace MolnApps\Testing\Response\Html;

use \MolnApps\Testing\Response\Value\PlainText;

abstract class AbstractNode implements Node
{
	protected $dom;
	
	public function matches(array $attributes)
	{
		foreach ($attributes as $attribute => $value) {
			if ( ! $this->hasAttribute($attribute, $value)) {
				return false;
			}
		}
		
		return true;
	}

	private function hasAttribute($name, $value)
	{
		if ($this->isSpecialAttribute($name)) {
			$methodName = 'has'.ucfirst($name);
			return $this->$methodName($value);
		}

		if (is_array($value)) {
			return in_array($this->getAttribute($name), $value);
		}

		return ($this->getAttribute($name) == $value);
	}

	private function isSpecialAttribute($name)
	{
		$specialAttributes = ['tag', 'text', 'class', 'child', 'children'];
		return in_array($name, $specialAttributes);
	}

	private function hasTag($tags)
	{
		return in_array($this->getTag(), (array)$tags);
	}

	abstract public function getTag();

	private function hasText($text)
	{
		foreach ((array)$text as $t) {
			if ( ! $this->hasTextString($t)) {
				return false;
			}
		}
		return true;
	}

	private function hasTextString($text)
	{
		return (
			$this->getText() == $this->getText($text) || 
			stripos($this->getText(), $this->getText($text)) !== false
		);
	}

	protected function getText($html = null)
	{
		$html = ( ! is_null($html)) ? $html : $this->innerHtml();

		return (new PlainText($html))->getText();
	}

	public function getTextWithNl($html = null)
	{
		$html = ! is_null($html) ? $html : $this->getInnerHtml();

		return (new PlainText($html))->getTextWithNl();
	}

	private function hasClass($classes)
	{
		if ( ! is_array($classes)) {
			$classes = split(' ', $classes);
		}

		$nodeClasses = explode(' ', (string)$this->getAttribute('class'));
		
		return ! array_diff($classes, $nodeClasses);
	}

	private function hasChildren(array $childrenAttributes)
	{
		foreach ($childrenAttributes as $attributes) {
			if ( ! $this->hasChild($attributes)) {
				return false;
			}
		}
		return true;
	}

	private function hasChild(array $attributes)
	{
		return !! $this->select($attributes)->find();
	}

	public function select(array $attributes)
	{
		$this->getDom()->select($attributes);

		return $this;
	}

	public function find($selector = null)
	{
		$collection = $this->getDom()->find($selector);

		$this->getDom()->reset();
		
		return $collection;
	}

	public function findFirst($selector = null)
	{
		$collection = $this->find($selector);

		if (count($collection) > 0) {
			return $collection[0];
		}
	}

	private function getDom()
	{
		if ( ! $this->dom) {
			$this->dom = $this->createDom();
		}

		return $this->dom;
	}

	protected function createDom()
	{
		return (new Dom)->load($this->innerHtml());
	}

	public function toHtml()
	{
		return $this->trim($this->getHtml()); 
	}

	public function innerHtml()
	{
		return $this->trim($this->getInnerHtml());
	}

	private function trim($value)
	{
		return str_replace(["\r", "\n", "\t"], '', $value);
	}

	abstract protected function getHtml();

	abstract protected function getInnerHtml();

	abstract public function getAttribute($name);

	public function setAttributes(array $attributes)
	{
		foreach ($attributes as $name => $value) {
			$this->setAttribute($name, $value);
		}
		
		return $this;
	}

	abstract public function setAttribute($name, $value);
}