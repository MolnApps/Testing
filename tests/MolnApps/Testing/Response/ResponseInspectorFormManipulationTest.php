<?php

namespace MolnApps\Testing\Response;

use \MolnApps\Testing\TestCase;

use \MolnApps\Testing\Response\Testing\ControllerStub;
use \MolnApps\Testing\Router\Router;

class ResponseInspectorFormManipulationTest extends TestCase
{
	protected function setUp() : void
	{
		$this->instance = new ResponseInspector(new ControllerStub, new Router);
	}

	/** @test */
	public function it_changes_an_input_value()
	{
		$this->instance
			->setResponse('
				<form id="myForm" action="index.php?cmd=Foo">
					<input name="note" value="Lorem ipsum" />
				</form>
			')
			->seeForm('myForm')
				->seeInput('note')
					->enter('Dolor sit amet')
			->submit()
				->seeText('Foo response')
				->seeText('Dolor sit amet');
	}

	/** @test */
	public function it_changes_a_textarea_value()
	{
		$this->instance
			->setResponse('
				<form id="myForm" action="index.php?cmd=Foo">
					<textarea name="note">Lorem ipsum</textarea>
				</form>
			')
			->seeForm('myForm')
				->seeTextarea('note')
					->enter('Dolor sit amet')
			->submit()
				->seeText('Foo response')
				->seeText('Dolor sit amet');
	}

	/** @test */
	public function it_changes_a_select_value()
	{
		$this->instance
			->setResponse('
				<form id="myForm" action="index.php?cmd=Bar">
					<select name="language">
						<option value="it_IT">Italian</option>
						<option value="de_DE" selected="selected">German</option>
						<option value="es_ES">Spanish</option>
					</select>
				</form>
			')
			->seeForm('myForm')
				->seeSelect('language')
					->choose('it_IT')
			->submit()
				->seeText('Bar response')
				->seeText('it_IT');
	}

	/** @test */
	public function it_changes_a_select_value_with_a_forged_value()
	{
		$this->instance
			->setResponse('
				<form id="myForm" action="index.php?cmd=Bar">
					<select name="language">
						<option value="it_IT">Italian</option>
						<option value="de_DE" selected="selected">German</option>
						<option value="es_ES">Spanish</option>
					</select>
				</form>
			')
			->seeForm('myForm')
				->seeSelect('language')
					->choose('jp_JP')
			->submit()
				->seeText('Bar response')
				->seeText('jp_JP');
	}

	/** @test */
	public function it_changes_a_mutiple_select_value()
	{
		$this->instance
			->setResponse('
				<form id="myForm" action="index.php?cmd=Bar">
					<select name="language[]" multiple>
						<option value="it_IT">Italian</option>
						<option value="de_DE" selected="selected">German</option>
						<option value="es_ES">Spanish</option>
					</select>
				</form>
			')
			->seeForm('myForm')
				->seeSelect('language[]')
					->choose(['it_IT', 'es_ES'])
			->submit()
				->seeText('Bar response')
				->dontSeeText('de_DE')
				->seeText('it_IT')
				->seeText('es_ES');
	}

	/** @test */
	public function it_changes_a_mutiple_select_value_with_forged_values()
	{
		$this->instance
			->setResponse('
				<form id="myForm" action="index.php?cmd=Bar">
					<select name="language[]" multiple>
						<option value="it_IT">Italian</option>
						<option value="de_DE" selected="selected">German</option>
						<option value="es_ES">Spanish</option>
					</select>
				</form>
			')
			->seeForm('myForm')
				->seeSelect('language[]')
					->choose(['jp_JP', 'it_IT', 'br_BR'])
			->submit()
				->seeText('Bar response')
				->dontSeeText('de_DE')
				->seeText('jp_JP')
				->seeText('it_IT')
				->seeText('br_BR');
	}

	/** @test */
	public function it_changes_a_checkbox_status_to_checked()
	{
		$this->instance
			->setResponse('
				<form id="myForm" action="index.php?cmd=Bar">
					<input type="checkbox" name="agree" value="agree" />
				</form>
			')
			->seeForm('myForm')
				->seeCheckbox('agree')
					->check()
			->submit()
				->seeText('Bar response')
				->seeText('agree');
	}

	/** @test */
	public function it_changes_a_checkbox_status_to_unchecked()
	{
		$this->instance
			->setResponse('
				<form id="myForm" action="index.php?cmd=Bar">
					<input type="checkbox" name="agree" value="agreed" checked="checked" />
				</form>
			')
			->seeForm('myForm')
				->seeCheckbox('agree')
					->uncheck()
			->submit()
				->seeText('Bar response')
				->dontSeeText('agreed');
	}

	/** @test */
	public function it_can_fill_a_form()
	{
		$this->instance
			->setResponse('
				<form id="myForm" action="index.php?cmd=Foo">
					<input name="firstName" />
					<input name="lastName" />
					<input name="jobTitle" />
					<textarea name="note"></textarea>
				</form>
			')
			->seeForm('myForm')
			->fillForm([
				'firstName' => 'George',
				'lastName' => 'Doe',
				'jobTitle' => 'Coder',
				'note' => 'Lorem ipsum dolor',
			])
			->submit()
				->seeText('Foo response')
				->seeText('George')
				->seeText('Doe')
				->seeText('Coder')
				->seeText('Lorem ipsum dolor');
	}

	/** @test */
	public function it_chooses_a_falsy_value_in_a_select()
	{
		$this->instance
			->setResponse('
				<form id="myForm" action="index.php?cmd=Foo">
					<select name="userStatus">
						<option value="1" selected="selected">Active</option>
						<option value="0">Blocked</option>
					</select>
				</form>
			')
			->seeForm('myForm')
				->seeSelect('userStatus')
					->choose('0')
			->submit()
				->seeText('Foo response')
				->seeText('userStatus = 0');
	}
}