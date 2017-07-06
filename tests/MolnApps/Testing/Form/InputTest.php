<?php

namespace MolnApps\Testing\Form;

use \MolnApps\Testing\Response\Html\DomNodeFactory;

class InputTest extends \PHPUnit_Framework_TestCase
{
	private $inputElement;

	protected function setUp()
	{
		$node =  DomNodeFactory::createElement('input', [
			'type' => 'text', 
			'name' => 'lastName', 
			'value' => 'Hello world'
		]);

		$this->inputElement = FormElementFactory::create($node);
	}

	/** @test */
	public function it_creates_a_text_input()
	{
		$this->assertInstanceOf(Input::class, $this->inputElement);
		$this->assertEquals('lastName', $this->inputElement->getName());
		$this->assertEquals('Hello world', $this->inputElement->getValue());
	}

	/** @test */
	public function it_changes_input_value()
	{
		$this->inputElement->setValue('Foobar 123');
		$this->assertEquals('Foobar 123', $this->inputElement->getValue());
	}

	/** @test */
	public function it_empties_input_value()
	{
		$this->inputElement->setValue('');
		$this->assertEquals('', $this->inputElement->getValue());
	}

	/** @test */
	public function it_sets_input_value_to_0()
	{
		$this->inputElement->setValue('0');
		$this->assertEquals('0', $this->inputElement->getValue());
	}

	/** @test */
	public function it_empties_input_value_with_null()
	{
		$this->inputElement->setValue(null);
		$this->assertEquals(null, $this->inputElement->getValue());
	}
}