<?php

namespace MolnApps\Testing\Response;

use \MolnApps\Testing\Response\Testing\ControllerStub;
use \MolnApps\Testing\Router\Router;

class ResponseInspectorTextTest extends \PHPUnit_Framework_TestCase
{
	protected function setUp()
	{
		$this->instance = new ResponseInspector(new ControllerStub, new Router);
	}

	/** @test */
	public function it_asserts_that_text_is_found()
	{
		$this->instance->setResponse('My text')->seeText('My text');
	}

	/** @test */
	public function it_throws_if_text_is_not_found()
	{
		$this->setExpectedException('\Exception', 'Could not find text [My text]');

		$this->instance->setResponse('')->seeText('My text');
	}

	/** @test */
	public function it_asserts_that_multiple_text_is_found()
	{
		$this->instance->setResponse('Lorem ipsum dolor')->seeText(['Lorem', 'ipsum', 'dolor']);
	}

	/** @test */
	public function it_throws_if_one_in_multiple_text_is_not_found()
	{
		$this->setExpectedException('\Exception', 'Could not find text [foo]');

		$this->instance->setResponse('Lorem ipsum dolor')->seeText(['Lorem', 'foo', 'dolor']);
	}

	/** @test */
	public function it_asserts_that_text_is_not_found()
	{
		$this->instance->setResponse('')->dontSeeText('My text');
	}

	/** @test */
	public function it_asserts_that_multiple_text_is_not_found()
	{
		$this->instance->setResponse('')->dontSeeText(['Dolor', 'sit', 'amet']);
	}

	/** @test */
	public function it_throws_if_text_is_found()
	{
		$this->setExpectedException('\Exception', 'Text [My text] was found');

		$this->instance->setResponse('My text')->dontSeeText('My text');
	}

	/** @test */
	public function it_throws_if_one_in_multiple_text_is_found()
	{
		$this->setExpectedException('\Exception', 'Text [sit] was found');

		$this->instance->setResponse('sit')->dontSeeText(['Dolor', 'sit', 'amet']);
	}
}