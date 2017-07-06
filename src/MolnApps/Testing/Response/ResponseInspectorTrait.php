<?php

namespace MolnApps\Testing\Response;

use \MolnApps\Testing\Response\ResponseInspector as ConcreteResponseInspector;
use \MolnApps\Testing\Router\Router;

trait ResponseInspectorTrait
{
	private $responseInspector;

	private $validToken = 'abc123';
	private $invalidToken = 'invalidToken';

	private $user;

	protected function getResponseInspector()
	{
		if ( ! $this->responseInspector) {
			$this->responseInspector = new ConcreteResponseInspector(
				$this->createController(),
				$this->createRouter()
			);
		}

		return $this->responseInspector;
	}

	abstract protected function createController();

	protected function createRouter()
	{
		return new Router;
	}

	protected function bootApplication()
	{
		$this->getResponseInspector()->bootApplication();

		return $this;
	}

	protected function shutdownApplication()
	{
		$this->getResponseInspector()->shutdownApplication();

		return $this;
	}

	protected function signedInAs($user)
	{
		$this->user = $user;

		$this->getResponseInspector()->signedInAs($user);

		return $this;
	}

	protected function getSignedInUser()
	{
		return $this->user;
	}

	protected function signedOut()
	{
		$this->user = null;

		$this->getResponseInspector()->signedOut();

		return $this;
	}

	protected function visit($command, array $params = [])
	{
		$params['cmd'] = $command;
		$params['token'] = $this->validToken;

		$this->getResponseInspector()->visit($params);
		
		return $this;
	}

	protected function visitWithoutToken($command, array $params = [])
	{
		$params['cmd'] = $command;
		$params['token'] = $this->invalidToken;

		$this->getResponseInspector()->visit($params);

		return $this;
	}

	protected function getResponse()
	{
		return $this->getResponseInspector()->getResponse();
	}

	protected function prophecy()
	{
		return $this->getResponseInspector()->prophecy();
	}

	protected function seeElement($selector = null, $attributes = [])
	{
		$this->getResponseInspector()->seeElement($selector);

		foreach ($attributes as $attribute => $value) {
			$this->withAttribute($attribute, $value);
		}

		return $this;
	}

	protected function shouldSeeElement($selector = null)
	{
		$this->getResponseInspector()->shouldSeeElement($selector);

		return $this;
	}

	protected function dontSeeElement($selector = null)
	{
		$this->getResponseInspector()->dontSeeElement($selector);

		return $this;
	}

	protected function withAttributes(array $attributes)
	{
		$this->getResponseInspector()->withAttributes($attributes);

		return $this;
	}

	protected function withAttribute($attribute, $value)
	{
		$this->getResponseInspector()->withAttribute($attribute, $value);

		return $this;
	}

	protected function withText($value)
	{
		return $this->withAttribute('text', $value);
	}

	protected function withChild($selector = null)
	{
		$this->getResponseInspector()->withChild($selector);
		
		return $this;
	}

	protected function ordered()
	{
		$this->getResponseInspector()->ordered();

		return $this;
	}

	protected function seeAtLeast()
	{
		$this->getResponseInspector()->atLeast();

		return $this;
	}

	protected function seeAtMost()
	{
		$this->getResponseInspector()->atMost();

		return $this;
	}

	protected function times($expectedCount)
	{
		$this->getResponseInspector()->times($expectedCount);

		return $this;
	}

	protected function seeOnce()
	{
		$this->getResponseInspector()->once();

		return $this;
	}

	protected function seeNever()
	{
		$this->getResponseInspector()->never();

		return $this;
	}

	protected function seeForm($formId)
	{
		$this->getResponseInspector()->seeForm($formId);

		return $this;
	}

	protected function seeIndexedField($name, $index)
	{
		$this->getResponseInspector()->seeArrayField($name, $index);

		return $this;
	}

	protected function dontSeeIndexedField($name, $index)
	{
		$this->getResponseInspector()->dontSeeArrayField($name, $index);

		return $this;
	}

	protected function seeIndexedInput($name, $index)
	{
		$this->getResponseInspector()->seeArrayInput($name, $index);

		return $this;
	}

	protected function dontSeeIndexedInput($name, $index)
	{
		$this->getResponseInspector()->dontSeeArrayInput($name, $index);

		return $this;
	}

	protected function seeInput($name)
	{
		$this->getResponseInspector()->seeInput($name);

		return $this;
	}

	protected function dontSeeInput($name)
	{
		$this->getResponseInspector()->dontSeeInput($name);

		return $this;
	}

	protected function seeSelect($name)
	{
		$this->getResponseInspector()->seeSelect($name);

		return $this;
	}

	protected function dontSeeSelect($name)
	{
		$this->getResponseInspector()->dontSeeSelect($name);

		return $this;
	}

	protected function withOptions(array $values)
	{
		$this->getResponseInspector()->withOptions($values);

		return $this;
	}

	protected function withoutOptions(array $values)
	{
		$this->getResponseInspector()->withoutOptions($values);

		return $this;
	}

	protected function seeTextarea($name)
	{
		$this->getResponseInspector()->seeTextarea($name);

		return $this;
	}

	protected function dontSeeTextarea($name)
	{
		$this->getResponseInspector()->dontSeeTextarea($name);

		return $this;
	}

	protected function seeCheckbox($name)
	{
		$this->getResponseInspector()->seeCheckbox($name);

		return $this;
	}

	protected function dontSeeCheckbox($name)
	{
		$this->getResponseInspector()->dontSeeCheckbox($name);

		return $this;
	}

	protected function seeFile($name)
	{
		$this->getResponseInspector()->seeFile($name);
		
		return $this;
	}

	protected function dontSeeFile($name)
	{
		$this->getResponseInspector()->dontSeeFile($name);
		
		return $this;
	}

	protected function seeText($text)
	{
		$this->getResponseInspector()->seeText($text);

		return $this;
	}

	protected function dontSeeText($text)
	{
		$this->getResponseInspector()->dontSeeText($text);

		return $this;
	}

	protected function withValue($value)
	{
		$this->getResponseInspector()->withValue($value);

		return $this;
	}

	protected function enter($inputValue)
	{
		$this->getResponseInspector()->enter($inputValue);

		return $this;
	}

	protected function check()
	{
		$this->getResponseInspector()->check();

		return $this;
	}

	protected function uncheck()
	{
		$this->getResponseInspector()->uncheck();

		return $this;
	}

	protected function fillForm(array $inputValues)
	{
		$this->getResponseInspector()->fillForm($inputValues);

		return $this;
	}

	protected function filledWith(array $inputValues)
	{
		$this->getResponseInspector()->filledWith($inputValues);

		return $this;
	}

	protected function choose($selectValue)
	{
		return $this->enter($selectValue);
	}

	protected function submit()
	{
		$this->getResponseInspector()->submit();

		return $this;
	}

	protected function submitWithoutToken($tokenInputName = 'token')
	{
		return $this->seeInput($tokenInputName)->enter($this->invalidToken)->submit();
	}

	protected function click($linkParams, array $overrideParams = [])
	{
		$this->seeLink($linkParams);
		$this->getResponseInspector()->click($overrideParams);

		return $this;
	}

	protected function clickWithoutToken($linkParams, $tokenInputName = 'token')
	{
		return $this->click($linkParams, [$tokenInputName => $this->invalidToken]);
	}

	protected function seeLink($linkParams)
	{
		$linkParams = (array)$linkParams;

		$text = $linkParams[0];
		$classOrId = isset($linkParams[1]) ? $linkParams[1] : null;
		
		$this->getResponseInspector()->seeLink($text, $classOrId);

		return $this;
	}

	protected function dump()
	{
		$this->getResponseInspector()->dump();

		return $this;
	}

	protected function dd()
	{
		$this->dump();
		die();
	}
}