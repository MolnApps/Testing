<?php

namespace MolnApps\Testing\Router;

class Router implements RouterInterface
{
	private $routes = [
		'get' => [],
		'post' => [],
	];

	private $prefix;

	public function prefix($prefix)
	{
		$this->prefix = $prefix;

		return $this;
	}

	public function get($uri, $commandName)
	{
		$this->routes['get'][$uri] = trim($commandName, '@');

		return $this;
	}

	public function post($uri, $commandName)
	{
		$this->routes['post'][$uri] = trim($commandName, '@');

		return $this;
	}

	public function direct($uri, $method = 'get')
	{
		$result = null;

		if (array_key_exists($method, $this->routes)) {
			$result = $this->directVerb($uri, $this->routes[$method]);
		}

		if ($result) {
			return $result;
		}

		throw new \Exception('No route defined for this uri.');
	}

	private function directVerb($uri, $array)
	{
		$uri = Uri::make($uri);
		$uriIdentifier = $uri->getIdentifier($this->prefix);

		foreach ($array as $identifier => $controller) {
			if ($this->matches($identifier, $uriIdentifier)) {
				return array_merge(
					$this->collectCommandParams($controller), 
					$this->collectWildcardParams($identifier, $uriIdentifier), 
					$this->collectQueryParams($uri)
				);
			}
		}
	}

	private function matches($identifier, $uri)
	{
		if ( ! $identifier) {
			return $identifier == $uri;
		}

		if ($identifier == $uri) {
			return true;
		}

		foreach ($this->merge($identifier, $uri) as $a => $b) {
			if ($a != $b && ! $this->isWildcard($a)) {
				return false;
			}
		}

		return true;
	}

	private function collectCommandParams($controller)
	{
		return ['cmd' => $controller];
	}

	private function collectWildcardParams($identifier, $uri)
	{
		$params = [];
		
		foreach ($this->merge($identifier, $uri) as $a => $b) {
			if ($this->isWildcard($a)) {
				$params[$this->cleanWildcard($a)] = $b;
			}
		}
		
		return $params;
	}

	private function collectQueryParams(Uri $uri)
	{
		return $uri->toArray();
	}

	private function merge($identifier, $uri)
	{
		$identifierParts = explode('/', $identifier);
		$uriParts = explode('/', $uri);
		
		if (count($identifierParts) != count($uriParts)) {
			$uriParts = array_fill(0, count($identifierParts), '');
		}
		
		return array_combine($identifierParts, $uriParts);
	}

	private function isWildcard($string)
	{
		return preg_match('/^{[a-zA-Z0-9]+}$/', $string);
	}

	private function cleanWildcard($string)
	{
		return trim($string, '{}');
	}
}