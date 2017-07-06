<?php

namespace MolnApps\Testing\Response\Html;

use \DomNode as BaseDomNode;
use \DomDocument;

class DomNodeFactory 
{
	public static function createElement($tag, $attributes)
	{
		return (new DomNode((new DomDocument)->createElement($tag)))->setAttributes($attributes);
	}

	public static function createDomNode($domNode)
	{
		if ($domNode instanceof DomNode) {
			return $domNode;
		}

		if ($domNode instanceof BaseDomNode) {
			return new DomNode($domNode);
		}
		
		if (is_string($domNode)) {
			$domDocument = new \DomDocument;
			$domDocument->loadHtml($domNode);
			$elements = $domDocument->getElementsByTagName('div');
			return new DomNode($elements[0]);
		}
		
		throw new \InvalidArgumentException('Invalid domNode type');
	}
}