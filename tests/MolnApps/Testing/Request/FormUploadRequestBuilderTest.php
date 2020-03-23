<?php

namespace MolnApps\Testing\Request;

use \MolnApps\Testing\Response\Html\DomNodeFactory;

use \MolnApps\Testing\Request\FormUploadRequestBuilder;

class FormUploadRequestBuilderTest extends \PHPUnit\Framework\TestCase
{
	/** @test */
	public function it_will_submit_file_inputs()
	{
		$avatarFileArray = $this->createFileArray('avatar.png');

		$form = $this->createNode('form', ['enctype' => 'multipart/form-data']);
		$this
			->addFileInput($form, 'avatar', $avatarFileArray)
			->addTextInput($form);

		$builder = new FormUploadRequestBuilder($form);
		
		$this->assertEquals(['avatar' => $avatarFileArray], $builder->getParams());
	}

	/** @test */
	public function it_will_submit_file_inputs_with_array_name()
	{
		$attachmentFileArray1 = $this->createFileArray('file1.png');
		$attachmentFileArray2 = $this->createFileArray('file2.png');
		
		$form = $this->createNode('form', ['enctype' => 'multipart/form-data']);
		$this
			->addFileInput($form, 'attachments[]', $attachmentFileArray1)
			->addFileInput($form, 'attachments[]', $attachmentFileArray2)
			->addTextInput($form);

		$builder = new FormUploadRequestBuilder($form);
		
		$this->assertEquals([
			'attachments' => [
				$attachmentFileArray1,
				$attachmentFileArray2,
			]
		], $builder->getParams());
	}

	/** @test */
	public function it_will_submit_file_inputs_with_indexed_array_name()
	{
		$attachmentFileArray1 = $this->createFileArray('file1.png');
		$attachmentFileArray2 = $this->createFileArray('file2.png');
		
		$form = $this->createNode('form', ['enctype' => 'multipart/form-data']);
		$this
			->addFileInput($form, 'attachments[2]', $attachmentFileArray1)
			->addFileInput($form, 'attachments[4]', $attachmentFileArray2)
			->addTextInput($form);

		$builder = new FormUploadRequestBuilder($form);
		
		$this->assertEquals([
			'attachments' => [
				'2' => $attachmentFileArray1,
				'4' => $attachmentFileArray2,
			]
		], $builder->getParams());
	}

	/** @test */
	public function it_will_not_submit_any_file_if_form_has_no_multipart_enctype()
	{
		$avatar = $this->createFileArray('file1.png');
		
		$form = $this->createNode('form', []);
		$this
			->addFileInput($form, 'avatar', $avatar)
			->addTextInput($form);

		$builder = new FormUploadRequestBuilder($form);
		
		$this->assertEquals([], $builder->getParams());
	}

	private function createFileArray($filename)
	{
		return [
			'name' => $filename,
			'tmp_name' => '/path/to/'.$filename,
			'size' => 1024,
			'type' => 'image/png',
			'error' => 0
		];
	}

	private function addFileInput($form, $name, $fileArray)
	{
		$form->addChild('input', [
			'type' => 'file', 
			'name' => $name, 
			'data-upload-file-json' => json_encode($fileArray),
		]);

		return $this;
	}

	private function addTextInput($form)
	{
		$form->addChild('input', [
			'name' => 'firstName',
			'type' => 'text',
			'value' => 'First name',
		]);

		return $this;
	}

	private function createNode($tag, array $attributes)
	{
		return DomNodeFactory::createElement($tag, $attributes);
	}
}