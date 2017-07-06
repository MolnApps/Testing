<?php

namespace MolnApps\Testing\Request;

use \MolnApps\Testing\Router\Router;
use \MolnApps\Testing\Router\Uri;

trait CollectsUrlParams
{
	private $router;

	public function withRouter(Router $router)
	{
		$this->router = $router;

		return $this;
	}

	public function collectUrlParams($url, $method = '')
	{
		if ($this->router) {
			try {
				return $this->router->direct($url, $method);
			} catch (\Exception $e) {
				// Do nothing
			}
		}

		return Uri::make($url)->toArray();
	}
}