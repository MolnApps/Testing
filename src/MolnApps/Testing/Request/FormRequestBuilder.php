<?php

namespace MolnApps\Testing\Request;

use \MolnApps\Testing\Response\Html\Node;

use \MolnApps\Testing\Form\FormElementFactory;
use \MolnApps\Testing\Form\InputName;

class FormRequestBuilder implements RequestBuilder
{
	use CollectsUrlParams;

	private $formElement;
	
	private $request;
	private $params = [];

	public function __construct(Node $formElement)
	{
		$this->formElement = $formElement;
	}

	public static function make(Node $formElement)
	{
		return new static($formElement);
	}

	public function getParams()
	{
		if ( ! $this->paramsWereCreated()) {
			$this->createParams();
		}

		return $this->params;
	}

	private function paramsWereCreated()
	{
		return !! $this->params;
	}

	private function createParams()
	{
		$this->params = [];

		$this->actionToParams();
		
		$this->elementValuesToParams('input');
		$this->elementValuesToParams('textarea');
		$this->elementValuesToParams('select');
		$this->elementValuesToParams('checkbox');
	}

	private function actionToParams()
	{
		$this->params = array_merge($this->params, $this->getActionParams());
	}

	private function getActionParams()
	{
		return $this->collectUrlParams($this->getFormElement()->getAttribute('action'), 'post');
	}

	private function elementValuesToParams($tag)
	{
		$elements = $this->getFormElement()->find($tag);

		foreach ($elements as $element) {
			$this->elementValueToParam($element);
		}
	}

	private function elementValueToParam($element)
	{
		$formElement = FormElementFactory::create($element);

		$inputName = new InputName($formElement->getName());

		$qualifiedName = $inputName->getQualifiedName();
		$qualifiedKey = $inputName->getQualifiedKey();

		foreach ((array)$formElement->getValue() as $value) {
			if ($this->isArray($inputName)) {
				$this->params[$qualifiedName][] = $value;
			} elseif ($this->isIndexedArray($inputName)) {
				// This is a dirty, quick fix to add support for multi dimensional array fields
				// such as src_filter[0][value]. 
				// It does not support src_filter[0][value][foo] though. 
				// We should write proper tests and use recursion.

				$queryRepresentation = 
					$qualifiedName . '[' . implode('][', (array)$qualifiedKey) . ']='.$value;
				
				parse_str($queryRepresentation, $result);
				
				$value = $result[$qualifiedName];
				
				if ( ! array_key_exists($qualifiedName, $this->params)) {
					$this->params[$qualifiedName] = [];
				}
				
				foreach ($value as $k => $v) {
					if (
						is_array($v) &&
						array_key_exists($k, $this->params[$qualifiedName]) && 
						is_array($this->params[$qualifiedName][$k])
					) {
						$v = array_merge($this->params[$qualifiedName][$k], $v);
					}
					$this->params[$qualifiedName][$k] = $v;
				}
			} else {
				$this->params[$qualifiedName] = $value;
			}
		}
	}

	private function isArray($inputName)
	{
		return $inputName->isArray() && ! $inputName->getQualifiedKey();
	}

	private function isIndexedArray($inputName)
	{
		return $inputName->isArray() && $inputName->getQualifiedKey();
	}

	private function getFormElement()
	{
		return $this->formElement;
	}
}