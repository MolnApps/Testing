<?php

namespace MolnApps\Testing\Router;

class Uri
{
	private $url = [];

	public function __construct($uriString)
	{
		$this->url = parse_url($uriString);
	}

	public static function make($uriString)
	{
		return new static($uriString);
	}

	public function toArray()
	{
		parse_str($this->getQuery(), $params);

		return $params;
	}

	public function getPath()
	{
		return $this->url['path'];
	}

	public function getQuery()
	{
		return isset($this->url['query']) ? $this->url['query'] : '';
	}

	public function getIdentifier($prefix = '')
	{
		$result = $this->getPath();
		$result = $this->removePrefix($result, $prefix);
		$result = $this->stripSlashes($result);
		return $result;
	}

	private function removePrefix($uri, $prefix)
	{
		return preg_replace('/^\/?' . $prefix . '/', '', $uri);
	}

	private function stripSlashes($uri)
	{
		return trim($uri, '/');
	}
}