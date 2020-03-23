<?php

namespace MolnApps\Testing\Form;

use \MolnApps\Testing\Response\Html\DomNodeFactory;

class TextareaTest extends \PHPUnit\Framework\TestCase
{
	private $textareaElement;

	protected function setUp() : void
	{
		$node = DomNodeFactory::createElement('textarea', [
			'name' => 'description', 'text' => 'Lorem ipsum dolor sit amet',
		]);

		$this->textareaElement = FormElementFactory::create($node);
	}

	/** @test */
	public function it_creates_a_textarea()
	{
		$this->assertInstanceOf(Textarea::class, $this->textareaElement);
		$this->assertEquals('description', $this->textareaElement->getName());
		$this->assertEquals('Lorem ipsum dolor sit amet', $this->textareaElement->getValue());
	}

	/** @test */
	public function it_changes_textarea_value()
	{
		$this->textareaElement->setValue('Foobar');
		$this->assertEquals('Foobar', $this->textareaElement->getValue());
	}

	/** @test */
	public function it_sets_textarea_value_with_newlines()
	{
		$string = 'Foo' . "\r\n" . 'Bar';
		$this->textareaElement->setValue($string);
		$this->assertEquals($string, $this->textareaElement->getValue());
	}

	/** @test */
	public function it_empties_textarea_value()
	{
		$this->textareaElement->setValue('');
		$this->assertEquals('', $this->textareaElement->getValue());
	}

	/** @test */
	public function it_sets_to_0_textarea_value()
	{
		$this->textareaElement->setValue('0');
		$this->assertEquals('0', $this->textareaElement->getValue());
	}

	/** @test */
	public function it_empties_textarea_value_with_null()
	{
		$this->textareaElement->setValue(null);
		$this->assertEquals(null, $this->textareaElement->getValue());
	}
}