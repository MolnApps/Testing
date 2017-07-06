<?php

namespace MolnApps\Testing\Form;

class InputNameTest extends \PHPUnit_Framework_TestCase
{
	/** @test */
	public function it_can_be_instantiated()
	{
		$instance = new InputName('lastName');
	}

	/** @test */
	public function it_gets_input_name()
	{
		$instance = new InputName('lastName');
		$this->assertEquals('lastName', $instance->getName());

		$instance = new InputName('meta[]');
		$this->assertEquals('meta[]', $instance->getName());

		$instance = new InputName('meta[1]');
		$this->assertEquals('meta[1]', $instance->getName());

		$instance = new InputName('src_filters[0][value]');
		$this->assertEquals('src_filters[0][value]', $instance->getName());
	}

	/** @test */
	public function it_gets_input_qualified_name()
	{
		$instance = new InputName('lastName');
		$this->assertEquals('lastName', $instance->getQualifiedName());

		$instance = new InputName('meta[]');
		$this->assertEquals('meta', $instance->getQualifiedName());

		$instance = new InputName('meta[1]');
		$this->assertEquals('meta', $instance->getQualifiedName());

		$instance = new InputName('src_filters[0][value]');
		$this->assertEquals('src_filters', $instance->getQualifiedName());
	}

	/** @test */
	public function it_gets_input_qualified_key()
	{
		$instance = new InputName('lastName');
		$this->assertEquals(null, $instance->getQualifiedKey());

		$instance = new InputName('meta[]');
		$this->assertEquals(null, $instance->getQualifiedKey());

		$instance = new InputName('meta[1]');
		$this->assertEquals('1', $instance->getQualifiedKey());

		$instance = new InputName('src_filters[0][value]');
		$this->assertEquals(['0', 'value'], $instance->getQualifiedKey());
	}

	/** @test */
	public function it_returns_if_input_is_array()
	{
		$instance = new InputName('lastName');
		$this->assertEquals(false, $instance->isArray());

		$instance = new InputName('meta[]');
		$this->assertEquals(true, $instance->isArray());

		$instance = new InputName('meta[1]');
		$this->assertEquals(true, $instance->isArray());

		$instance = new InputName('src_filters[0][value]');
		$this->assertEquals(true, $instance->isArray());
	}
}