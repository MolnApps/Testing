<?php

namespace MolnApps\Testing\Router;

interface RouterInterface
{
	public function prefix($prefix);
	public function get($uri, $commandName);
	public function post($uri, $commandName);
	public function direct($uri, $method = 'get');
}