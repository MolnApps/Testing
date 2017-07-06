<?php

namespace MolnApps\Testing\Response\Json;

class JsonResponse
{
	private $response;
	
	public function __construct($json)
	{
		$this->response = json_decode($json);
	}

	public function __get($name)
	{
		return $this->getProperty($name);
	}

	public function getProperty($name)
	{
		if (isset($this->response->$name)) {
			if ($this->shouldReturnNewInstance($this->response->$name)) {
				return $this->newInstance($this->response->$name);
			} else {
				return $this->response->$name;
			}
		} else {
			return null;
		}
	}

	public function item($index)
	{
		if (isset($this->response[$index])) {
			return $this->newInstance($this->response[$index]);
		}
	}

	public function toArray()
	{
		$result = [];

		foreach ($this->response as $key => $value) {
			if ($this->shouldReturnNewInstance($value)) {
				$value = $this->newInstance($value)->toArray();
			}
			$result[$key] = $value;
		}

		return $result;
	}

	private function shouldReturnNewInstance($value)
	{
		return is_object($value) || is_array($value);
	}

	private function newInstance($value)
	{
		return new static(json_encode($value));
	}
}