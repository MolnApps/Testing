<?php

use \MolnApps\Testing\Response\JsonResponseInspector;
use \MolnApps\Testing\Response\Controller;

class JsonResponseInspectorTest extends PHPUnit_Framework_TestCase
{
	private $controller;
	private $inspector;

	protected function setUp()
	{
		$this->controller = $this->getMockBuilder(Controller::class)->setMethods(['run', 'bootApplication', 'shutdownApplication', 'signedInAs', 'signedOut'])->getMock();
		$this->inspector = new JsonResponseInspector($this->controller);
	}

	protected function tearDown()
	{
		//Mockery::close();
	}

	/** @test */
	public function it_can_be_instantiated()
	{
		$inspector = new JsonResponseInspector();

		$this->assertNotNull($inspector);
	}

	/** @test */
	public function it_can_be_instantiated_with_controller()
	{
		$inspector = new JsonResponseInspector($this->controller);

		$this->assertNotNull($inspector);
	}

	/** @test */
	public function it_runs_a_command_and_stores_the_response()
	{
		$response = json_encode(['success' => 1]);

		$this->controllerReturns($response);

		$this->inspector->visit('MyCommand');

		$this->assertEquals($response, $this->inspector->getResponse());
	}

	/** @test */
	public function it_runs_a_command_and_inspects_the_response()
	{
		$this->setResponse([
			'success' => 1
		]);

		$this->assertEquals(1, $this->inspector->response()->success);
	}

	/** @test */
	public function it_returns_null_if_response_has_no_such_key()
	{
		$this->setResponse([
			'success' => 1
		]);

		$this->assertNull($this->inspector->response()->foobar);
	}

	/** @test */
	public function it_runs_a_command_and_inspects_nested_response()
	{
		$this->setResponse([
			'success' => 1,
			'feedback' => [
				'message' => 'Hello world',
				'code' => 101,
			]
		]);

		$this->assertEquals('Hello world', $this->inspector->response()->feedback->message);
		$this->assertEquals(101, $this->inspector->response()->feedback->code);
	}

	/** @test */
	public function it_runs_a_command_and_convert_nested_response_to_array()
	{
		$this->setResponse([
			'success' => 1,
			'feedback' => [
				'message' => 'Hello world',
				'code' => 101,
			]
		]);

		$this->assertEquals(
			['message' => 'Hello world', 'code' => 101], 
			$this->inspector->response()->feedback->toArray()
		);
	}

	/** @test */
	public function it_runs_a_command_and_recursively_convert_nested_response_with_keys_to_array()
	{
		$this->setResponse([
			'success' => 1,
			'feedbacks' => [
				'first' => [
					'message' => 'Hello world',
					'code' => 101,
				],
				'second' => [
					'message' => 'Success',
					'code' => 102,
				]
			]
		]);

		$this->assertEquals(
			[
				'first' => ['message' => 'Hello world', 'code' => 101],
				'second' => ['message' => 'Success', 'code' => 102],
			], 
			$this->inspector->response()->feedbacks->toArray()
		);
	}

	/** @test */
	public function it_runs_a_command_and_recursively_convert_nested_response_with_indexes_to_array()
	{
		$this->setResponse([
			'success' => 1,
			'feedbacks' => [
				0 => [
					'message' => 'Hello world',
					'code' => 101,
				],
				1 => [
					'message' => 'Success',
					'code' => 102,
				]
			]
		]);

		$this->assertEquals(
			[
				0 => ['message' => 'Hello world', 'code' => 101],
				1 => ['message' => 'Success', 'code' => 102],
			], 
			$this->inspector->response()->feedbacks->toArray()
		);
	}

	/** @test */
	public function it_runs_a_command_and_access_array_index_property()
	{
		$this->setResponse([
			'success' => 1,
			'feedbacks' => [
				0 => [
					'message' => 'Hello world',
					'code' => 101,
				],
				1 => [
					'message' => 'Success',
					'code' => 102,
				]
			]
		]);

		$this->assertEquals('Hello world', $this->inspector->response()->feedbacks->item(0)->message);
		$this->assertEquals('Success', $this->inspector->response()->feedbacks->item(1)->message);
	}

	// ! Utility methods

	private function setResponse(array $response)
	{
		$this->inspector->setResponse(json_encode($response));
	}

	private function controllerReturns($jsonResponse)
	{
		 $this->controller->expects($this->once())->method('run')->willReturn($jsonResponse);
	}
}