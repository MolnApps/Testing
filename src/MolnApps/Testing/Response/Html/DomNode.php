<?php

namespace MolnApps\Testing\Response\Html;

use \DomNode as BaseDomNode;

class DomNode extends AbstractNode
{
	private $domNode;

	public function __construct(BaseDomNode $domNode)
	{
		$this->domNode = $domNode;
	}

	public function getElement()
	{
		return $this->domNode;
	}

	public function appendChild(Node $childNode)
	{
		$baseNode = $childNode->getElement();
		$baseNode = $this->domNode->ownerDocument->importNode($baseNode, true);
		$this->domNode->appendChild($baseNode);

		$this->dom = null;
	}

	public function addChild($tag, array $attributes = [])
	{
		$childNode = new static($this->domNode->ownerDocument->createElement($tag));
		
		foreach ($attributes as $name => $value) {
			$childNode->setAttribute($name, $value);
		}

		$this->domNode->appendChild($childNode->getElement());

		$this->dom = null;

		return $this;
	}

	public function getTag()
	{
		return $this->domNode->nodeName;
	}

	public function getAttribute($name)
	{
		if ($name == 'text') {
			return $this->getText();
		}

		if ($this->domNode->hasAttribute($name)) {
			return $this->domNode->getAttribute($name);
		}
	}

	public function getAttributes()
	{
		$result = [];

		foreach ($this->domNode->attributes as $name => $value) {
			$result[$name] = $value;
		}
		
		return $result;
	}

	public function setAttribute($name, $value)
	{
		if ($name == 'text') {
			$this->domNode->textContent = $value;

			return;
		}

		if ( is_null($value)) {
			$this->removeAttribute($name);
			return;
		}

		$this->domNode->setAttribute($name, (string)$value);
	}

	private function removeAttribute($name)
	{
		$this->domNode->removeAttribute($name);
	}

	protected function getHtml()
	{
		return $this->domNode->ownerDocument->saveHTML($this->domNode);
	}

	protected function getInnerHtml()
	{
		$innerHtml = ''; 

		foreach ($this->domNode->childNodes as $child) 
	    {
	    	$innerHtml .= $this->domNode->ownerDocument->saveHTML($child);
	    }

	    $innerHtml = str_replace("&#13;", "\r", $innerHtml);

	    return $innerHtml;
	}

	protected function createDom()
	{
		return new Dom(null, new OldDomParser($this->domNode->ownerDocument, $this->domNode));
	}
}