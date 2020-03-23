<?php

namespace MolnApps\Testing\Form;

use \PHPUnit\Framework\TestCase;

use \MolnApps\Testing\Response\Html\DomNodeFactory;

class FileTest extends \PHPUnit\Framework\TestCase
{
	private $fileElement;

	protected function setUp() : void
	{
		$node = DomNodeFactory::createElement('input', [
			'type' => 'file', 
			'name' => 'avatar',
			'data-upload-file-json' => '',
		]);

		$this->fileElement = FormElementFactory::create($node);
	}

	/** @test */
	public function it_creates_a_file_input()
	{
		$this->assertInstanceOf(File::class, $this->fileElement);
		$this->assertEquals('avatar', $this->fileElement->getName());
		$this->assertEquals(null, $this->fileElement->getValue());
	}

	/** @test */
	public function it_changes_file_value()
	{
		$this->fileElement->setValue([
			'tmp_name' => '/path/to/image.png',
			'name' => 'image.png',
			'type' => 'image/png',
			'size' => 1024,
			'error' => 0
		]);
		
		$this->assertEquals([
			'tmp_name' => '/path/to/image.png',
			'name' => 'image.png',
			'type' => 'image/png',
			'size' => 1024,
			'error' => 0
		], $this->fileElement->getValue());
	}

	/** @test */
	public function it_empties_textarea_value()
	{
		$this->fileElement->setValue('');
		$this->assertEquals(null, $this->fileElement->getValue());
	}

	/** @test */
	public function it_sets_to_0_textarea_value()
	{
		$this->fileElement->setValue('0');
		$this->assertEquals(null, $this->fileElement->getValue());
	}

	/** @test */
	public function it_empties_textarea_value_with_null()
	{
		$this->fileElement->setValue(null);
		$this->assertEquals(null, $this->fileElement->getValue());
	}

	/** @test */
	public function it_empties_textarea_value_with_empty_array()
	{
		$this->fileElement->setValue([]);
		$this->assertEquals(null, $this->fileElement->getValue());
	}
}