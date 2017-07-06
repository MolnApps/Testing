<?php

namespace MolnApps\Testing\Request;

use \MolnApps\Testing\Response\Html\Node;

class LinkRequestBuilder implements RequestBuilder
{
	use CollectsUrlParams;

	private $url;

	public function __construct(Node $linkElement)
	{
		$this->url = $linkElement->getAttribute('href');
	}

	public static function make(Node $linkElement)
	{
		return new static($linkElement);
	}

	public function getParams()
	{
		return $this->collectUrlParams($this->url, 'get');
	}
}