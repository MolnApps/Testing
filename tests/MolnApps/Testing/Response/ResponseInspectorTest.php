<?php

namespace MolnApps\Testing\Response;

use \MolnApps\Testing\Response\Testing\ControllerStub;
use \MolnApps\Testing\Router\Router;

class ResponseInspectorTest extends \PHPUnit_Framework_TestCase
{
	/** @before */
	protected function setUpInstance()
	{
		$this->controller = new ControllerStub;
		$this->instance = new ResponseInspector($this->controller, new Router);
	}

	/** @test */
	public function it_can_be_instantiated()
	{
		$this->assertNotNull($this->instance);
	}

	/** @test */
	public function it_can_boot_an_application()
	{
		$this->instance->bootApplication();

		$this->assertTrue($this->controller->wasBooted());
	}

	/** @test */
	public function it_can_shutdown_an_application()
	{
		$this->instance->bootApplication();

		$this->assertTrue($this->controller->wasBooted());

		$this->instance->shutdownApplication();

		$this->assertFalse($this->controller->wasBooted());
	}

	/** @test */
	public function it_can_sign_in_as_a_user()
	{
		$this->instance->signedInAs(1);

		$this->assertTrue($this->controller->isSignedIn());
	}

	/** @test */
	public function it_can_signout_a_user()
	{
		$this->instance->signedInAs(1);

		$this->assertTrue($this->controller->isSignedIn());

		$this->instance->signedOut();

		$this->assertFalse($this->controller->isSignedIn());
	}
	
	/** @test */
	public function it_visits_a_page_and_returns_a_response()
	{
		$this->instance
			->visit(['cmd' => 'Baz'])
			->seeText('Baz response');
	}

	/** @test */
	public function it_submits_a_form()
	{
		$this->instance
			->setResponse('
				<form id="myForm" action="index.php?cmd=Foo">
					<textarea name="note">Lorem ipsum</textarea>
				</form>
			')
			->seeForm('myForm')
			->submit()
				->seeText('Foo response')
				->seeText('Lorem ipsum');
	}

	/** @test */
	public function it_submits_a_form_with_array_field()
	{
		$this->instance
			->setResponse('
				<form id="myForm" action="index.php?cmd=Foo">
					<select name="labels[]" multiple>
						<option value="1" selected="selected">Label 1</option>
						<option value="2">Label 2</option>
						<option value="3" selected="selected">Label 3</option>
						<option value="4">Label 4</option>
					</select>
				</form>
			')
			->seeForm('myForm')
			->submit()
				->seeText('Foo response')
				->seeText('labels = [0, 1][1, 3]');
	}

	/** @test */
	public function it_submits_a_form_with_array_fields()
	{
		$this->instance
			->setResponse('
				<form id="myForm" action="index.php?cmd=Foo">
					<input type="text" name="meta[]" value="Meta 1">
					<input type="text" name="meta[]" value="Meta 2">
					<input type="text" name="meta[]" value="Meta 3">
					<input type="text" name="meta[]" value="Meta 4">
				</form>
			')
			->seeForm('myForm')
			->submit()
				->seeText('Foo response')
				->seeText('meta = [0, 1, 2, 3][Meta 1, Meta 2, Meta 3, Meta 4]');
	}

	/** @test */
	public function it_submits_a_form_with_indexed_array_field()
	{
		$this->instance
			->setResponse('
				<form id="myForm" action="index.php?cmd=Foo">
					<input type="text" name="meta[1]" value="Meta 1">
					<input type="text" name="meta[2]" value="Meta 2">
					<input type="text" name="meta[3]" value="Meta 3">
					<input type="text" name="meta[4]" value="Meta 4">
				</form>
			')
			->seeForm('myForm')
			->submit()
				->seeText('Foo response')
				->seeText('meta = [1, 2, 3, 4][Meta 1, Meta 2, Meta 3, Meta 4]');
	}

	/** @test */
	public function it_submits_a_form_with_uploaded_file()
	{
		$uploadedFile = [
			'tmp_name' => '/path/to/avatar.png',
			'name' => 'avatar.png',
			'type' => 'image/png',
			'size' => 1024,
			'error' => 0
		];

		$this->instance
			->setResponse('
				<form id="myForm" action="index.php?cmd=Foo" enctype="multipart/form-data">
					<input type="file" name="avatar">
				</form>
			')
			->seeForm('myForm')
			->seeFile('avatar')
				->enter($uploadedFile)
			->submit()
				->seeText('Foo response')
				->seeText(json_encode($uploadedFile));
	}

	/** @test */
	public function it_clicks_a_link()
	{
		$this->instance
			->setResponse('<a href="index.php?cmd=Baz">Baz</a>')
			->seeLink('Baz')
			->click()
			->seeText('Baz response');
	}

	/** @test */
	public function it_clicks_a_link_overriding_some_parameters()
	{
		$this->instance
			->setResponse('<a href="index.php?cmd=Foo">Foo</a>')
			->seeLink('Foo')
			->click(['cmd' => 'Bar'])
			->seeText('Bar response');
	}
}