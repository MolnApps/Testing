<?php

namespace MolnApps\Testing\Request;

use \MolnApps\Testing\Response\Html\DomNodeFactory;
use \MolnApps\Testing\Router\Router;

class LinkRequestBuilderTest extends \PHPUnit_Framework_TestCase
{
	/** @test */
	public function it_creates_a_request_array()
	{
		$link = $this->createNode('a', ['href' => 'index.php?cmd=MyCommand&confirm=false&recordId=12']);
		
		$builder = new LinkRequestBuilder($link);

		$this->assertEquals([
			'cmd' => 'MyCommand', 
			'confirm' => 'false', 
			'recordId' => '12'
		], $builder->getParams());
	}

	/** @test */
	public function it_creates_a_empty_request_array_with_anchor()
	{
		$link = $this->createNode('a', ['href' => '#']);
		
		$builder = new LinkRequestBuilder($link);

		$this->assertEquals([], $builder->getParams());
	}

	/** @test */
	public function it_creates_a_request_array_for_a_router_identifier_with_prefix()
	{
		$link = $this->createNode('a', ['href' => '/report/plans']);
		$router = $this->createRouter();

		$builder = LinkRequestBuilder::make($link)->withRouter($router);

		$this->assertEquals(['cmd' => 'Plans'], $builder->getParams());
	}

	/** @test */
	public function it_creates_a_request_array_for_a_router_identifier_without_prefix()
	{
		$link = $this->createNode('a', ['href' => 'plans']);
		$router = $this->createRouter();

		$builder = LinkRequestBuilder::make($link)->withRouter($router);

		$this->assertEquals(['cmd' => 'Plans'], $builder->getParams());
	}

	/** @test */
	public function it_creates_a_request_array_for_a_regular_link_when_router_is_set()
	{
		$link = $this->createNode('a', ['href' => 'index.php?cmd=Plans']);
		$router = $this->createRouter();

		$builder = LinkRequestBuilder::make($link)->withRouter($router);

		$this->assertEquals(['cmd' => 'Plans'], $builder->getParams());
	}

	// ! Utility methods

	private function createNode($tag, array $attributes)
	{
		return DomNodeFactory::createElement($tag, $attributes);
	}

	private function createRouter()
	{
		$router = new Router;
		$router->prefix('report');
		$router->get('plans', '@Plans');
		return $router;
	}
}