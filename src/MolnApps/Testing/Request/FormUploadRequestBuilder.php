<?php

namespace MolnApps\Testing\Request;

use \MolnApps\Testing\Response\Html\Node;
use \MolnApps\Testing\Form\InputName;

class FormUploadRequestBuilder implements RequestBuilder
{
	private $formElement;

	private $params = [];
	
	public function __construct(Node $formElement)
	{
		$this->formElement = $formElement;
	}

	public function getParams()
	{
		if ( ! $this->formIsMultipart()) {
			return [];
		}

		$this->params = [];

		foreach ($this->getAllFileInputs() as $input) {
			$this->addParam($input->getAttribute('name'), $input->getAttribute('data-upload-file-json'));
		}

		return $this->params;
	}

	private function formIsMultipart()
	{
		return $this->getFormElement()->getAttribute('enctype') == 'multipart/form-data';
	}

	private function getAllFileInputs()
	{
		return $this->getFormElement()->find('input[type="file"]');
	}

	private function addParam($name, $uploadedFileJson)
	{
		$uploadedFileArray = $this->jsonToArray($uploadedFileJson);

		if ($this->nameIsArray($name)) {
			$key = $this->getQualifiedKey($name);
			$name = $this->getQualifiedName($name);
			if ($key) {
				$this->params[$name][$key] = $uploadedFileArray;
			} else {
				$this->params[$name][] = $uploadedFileArray;
			}
		} else {
			$this->params[$name] = $uploadedFileArray;
		}
	}

	private function jsonToArray($json)
	{
		if ( ! $json) {
			return;
		}
		
		return (array)json_decode($json);
	}

	private function nameIsArray($name)
	{
		return InputName::make($name)->isArray();
	}

	private function getQualifiedKey($name)
	{
		return InputName::make($name)->getQualifiedKey();
	}

	private function getQualifiedName($name)
	{
		return InputName::make($name)->getQualifiedName();
	}

	private function getFormElement()
	{
		return $this->formElement;
	}
}