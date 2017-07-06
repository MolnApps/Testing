<?php

namespace MolnApps\Testing\Form;

use \MolnApps\Testing\Response\Html\DomNodeFactory;

class CheckboxTest extends \PHPUnit_Framework_TestCase
{
	private $checkboxElement;

	protected function setUp()
	{
		$node = DomNodeFactory::createElement('input', [
			'type' => 'checkbox', 'name' => 'agree', 'value' => 'agreenment'
		]);

		$this->checkboxElement = FormElementFactory::create($node);
	}

	/** @test */
	public function it_creates_a_checkbox()
	{
		$this->assertInstanceOf(Checkbox::class, $this->checkboxElement);
		$this->assertEquals('agree', $this->checkboxElement->getName());
	}

	/** @test */
	public function it_checks_a_checkbox()
	{
		$this->checkboxElement->setValue(true);
		$this->assertEquals('agreenment', $this->checkboxElement->getValue());
	}

	/** @test */
	public function it_unchecks_a_checkbox()
	{
		$this->checkboxElement->setValue(false);
		$this->assertEquals('', $this->checkboxElement->getValue());
	}
}