<?php

namespace MolnApps\Testing\Response;

use \MolnApps\Testing\TestCase;

use \MolnApps\Testing\Response\Testing\ControllerStub;
use \MolnApps\Testing\Router\Router;

class ResponseInspectorLinkTest extends TestCase
{
	protected function setUp() : void
	{
		$this->instance = new ResponseInspector(new ControllerStub, new Router);
	}

	/** @test */
	public function it_asserts_that_a_link_with_a_given_text_is_found()
	{
		$this->instance
			->setResponse('<a href="index.php?cmd=baz">Baz</a>')
			->seeLink('Baz');
	}

	/** @test */
	public function it_asserts_that_a_link_with_inner_html_is_found()
	{
		$this->instance
			->setResponse('<a href="index.php?cmd=Baz"><strong>Baz</strong> <em>Bar</em></a>')
			->seeLink('Baz Bar');
	}

	/** @test */
	public function it_throws_if_a_link_with_a_given_text_is_not_found()
	{
		$message = 'No element [a] was found with attributes [text = "Baz"]';
		$message = 'Element a[text="Baz"] was expected at least 1 time';
		$this->setExpectedException('\Exception', $message);

		$this->instance
			->setResponse('<a href="index.php?cmd=baz">Bar</a>')
			->seeLink('Baz');
	}
}