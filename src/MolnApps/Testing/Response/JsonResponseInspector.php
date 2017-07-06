<?php

namespace MolnApps\Testing\Response;

use \MolnApps\Testing\Response\Json\JsonResponse;

class JsonResponseInspector
{
	private $controller;
	private $response;

	public function __construct(Controller $controller = null)
	{
		$this->controller = $controller;
	}

	public function visit($command)
	{
		if ( ! $this->controller) {
			throw new \Exception('Please provide a controller.');
		}
		
		$requestParams = ['cmd' => $command];

		$response = $this->controller->run($requestParams);

		$this->setResponse($response);
	}

	public function setResponse($response)
	{
		$this->response = $response;

		return $this;
	}

	public function getResponse()
	{
		return $this->response;
	}

	public function response()
	{
		return new JsonResponse($this->response);
	}
}