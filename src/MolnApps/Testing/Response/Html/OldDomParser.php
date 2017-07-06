<?php

namespace MolnApps\Testing\Response\Html;

class OldDomParser implements DomParser
{
	private $domDocument;
	private $domElement;

	public function __construct(\DomDocument $domDocument = null, \DomElement $domElement = null)
	{
		$this->domDocument = ($domDocument) ?: new \DomDocument;
		$this->domDocument->preserveWhiteSpace = false;
		$this->domDocument->formatOutput = true;
		$this->domDocument->strictErrorChecking = false;

		$this->domElement = $domElement;
	}

	public function load($markup)
	{
		if ( ! $markup) {
			return;
		}

		$previous_value = libxml_use_internal_errors(TRUE);
		
		$this->domDocument->loadHtml($markup);
		
		libxml_clear_errors();
		libxml_use_internal_errors($previous_value);
	}

	public function find($selectors)
	{
		if (is_string($selectors)) {
			$selectors = (new Selector($selectors))->toArray();
		};

		if ($selectors instanceof Selector) {
			$selectors = $selectors->toArray();
		};

		$result = [];

		foreach ($selectors as $selector) {
			$result = array_merge($result, $this->findWithSelector($selector));
		}

		return $result;
	}

	private function findWithSelector($selector)
	{
		$tags = $this->normalizeTags($selector);

		$result = [];

		foreach ($this->getDomNodes($tags) as $domNode) {
			$domNode = DomNodeFactory::createDomNode($domNode);
			if ($domNode->matches($selector)) {
				$result[] = $domNode;
			}
		}
		
		return $result;
	}

	private function getDomNodes(array $tags)
	{
		$root = ($this->domElement) ?: $this->domDocument;

		$documentNodes = $this->getAllDomElements($root);

		$result = [];

		foreach ($documentNodes as $node) {
			if (in_array($node->tagName, $tags)) {
				$result[] = $node;
			}
		}

		return $result;
	}

	private function getAllDomElements(\DomNode $domNode)
	{
		$result = [];

		foreach ($domNode->childNodes as $childNode) {
			if ($childNode instanceof \DomElement) {
				$result[] = $childNode;
				$result = array_merge($result, $this->getAllDomElements($childNode));
			}
		}
		
		return $result;
	}

	private function normalizeTags($selector)
	{
		if ( ! isset($selector['tag'])) {
			return ['div', 'p', 'a'];
		}
		return (array)$selector['tag'];
	}

	private function getDomNodesArray(\DomNodeList $domNodeList)
	{
		$domNodesArray = [];
		
		foreach ($domNodeList as $domNode) {
			$domNodesArray[] = $domNode;
		}
		
		return $domNodesArray;
	}
}