<?php

namespace MolnApps\Testing\Form;

use \MolnApps\Testing\Response\Html\DomNodeFactory;

class SelectTest extends \PHPUnit\Framework\TestCase
{
	protected function setUp() : void
	{
		$this->select = DomNodeFactory::createElement('select', ['name' => 'country']);
		
		$this->select->addChild('option', ['value' => 'de_DE']);
		$this->select->addChild('option', ['value' => 'en_US', 'selected' => 'selected']);
		$this->select->addChild('option', ['value' => 'it_IT']);

		$this->selectElement = FormElementFactory::create($this->select);
	}
	
	/** @test */
	public function it_creates_a_select()
	{
		$this->assertInstanceOf(Select::class, $this->selectElement);
		$this->assertEquals('country', $this->selectElement->getName());
		$this->assertEquals('en_US', $this->selectElement->getValue());		
	}

	/** @test */
	public function it_selects_existing_option()
	{
		$this->selectElement->setValue('it_IT');
		$this->assertEquals('it_IT', $this->selectElement->getValue());
	}

	/** @test */
	public function it_creates_a_new_option_if_unknown_value_is_set()
	{
		$this->selectElement->setValue('jp_JP');
		$this->assertEquals('jp_JP', $this->selectElement->getValue());
	}

	/** @test */
	public function it_returns_first_item_if_empty_string_is_set_as_value()
	{
		$this->selectElement->setValue('');
		$this->assertEquals('de_DE', $this->selectElement->getValue());
	}

	/** @test */
	public function it_returns_first_item_if_null_is_set_as_value()
	{
		$this->selectElement->setValue(null);
		$this->assertEquals('de_DE', $this->selectElement->getValue());
	}

	/** @test */
	public function it_creates_a_new_option_with_0()
	{
		$this->selectElement->setValue('0');
		$this->assertEquals('0', $this->selectElement->getValue());
	}

	/** @test */
	public function it_selects_an_option_with_value_0()
	{
		$this->select->addChild('option', ['value' => '0']);

		$this->selectElement->setValue('0');
		$this->assertEquals('0', $this->selectElement->getValue());
	}
}