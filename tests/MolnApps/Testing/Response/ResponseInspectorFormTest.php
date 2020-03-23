<?php

namespace MolnApps\Testing\Response;

use \MolnApps\Testing\TestCase;

use \MolnApps\Testing\Response\Testing\ControllerStub;
use \MolnApps\Testing\Router\Router;

class ResponseInspectorFormTest extends TestCase
{
	protected function setUp() : void
	{
		$this->instance = new ResponseInspector(new ControllerStub, new Router);
	}

	/** @test */
	public function it_asserts_if_a_form_is_found()
	{
		$this->instance
			->setResponse('<form id="myForm"></form>')
			->seeForm('myForm');
	}

	/** @test */
	public function it_throws_if_any_form_is_not_found()
	{
		$message = 'No element [form] was found';
		$message = 'Element form was expected at least 1 time';
		$this->setExpectedException('\Exception', $message);

		$this->instance
			->setResponse('')
			->seeForm('myForm');
	}

	/** @test */
	public function it_throws_if_a_form_is_not_found()
	{
		$message = 'No element [form] was found with attributes [id = "myForm"]';
		$message = 'Element form#myForm was expected at least 1 time';
		$this->setExpectedException('\Exception', $message);

		$this->instance
			->setResponse('<form id="foo"></form>')
			->seeForm('myForm');
	}

	/** @test */
	public function it_asserts_that_an_input_is_found_within_a_form()
	{
		$this->instance
			->setResponse('
				<form id="myForm">
					<input type="text" name="clientName" />
				</form>
			')
			->seeForm('myForm')
				->seeInput('clientName');
	}

	/** @test */
	public function it_throws_if_input_is_not_found_within_a_form()
	{
		$this->setExpectedException('\Exception', 'Could not find input[name="clientName"]');

		$this->instance
			->setResponse('
				<form id="myForm"></form>
			')
			->seeForm('myForm')
				->seeInput('clientName');
	}

	/** @test */
	public function it_asserts_that_an_input_is_not_found_within_a_form()
	{
		$this->instance
			->setResponse('
				<form id="myForm">
					<input type="text" name="clientName" />
				</form>
			')
			->seeForm('myForm')
				->dontSeeInput('clientSupplier');
	}

	/** @test */
	public function it_throws_if_input_is_found_within_a_form()
	{
		$this->setExpectedException('\Exception', 'Element input[name="clientName"] was found');

		$this->instance
			->setResponse('
				<form id="myForm">
					<input type="text" name="clientName" />
				</form>
			')
			->seeForm('myForm')
				->dontSeeInput('clientName');
	}

	/** @test */
	public function it_asserts_that_an_array_input_is_found_within_a_form()
	{
		$this->instance
			->setResponse('
				<form id="myForm">
					<input type="text" name="meta[1]" />
				</form>
			')
			->seeForm('myForm')
				->seeArrayInput('meta', 1);
	}

	/** @test */
	public function it_throws_if_an_array_input_is_not_found_within_a_form()
	{
		$this->setExpectedException('\Exception', 'Could not find input[name="meta[2]"]');

		$this->instance
			->setResponse('
				<form id="myForm">
					<input type="text" name="meta[1]" />
				</form>
			')
			->seeForm('myForm')
				->seeArrayInput('meta', 2);
	}

	/** @test */
	public function it_asserts_that_an_array_input_is_not_found_within_a_form()
	{
		$this->instance
			->setResponse('
				<form id="myForm">
					<input type="text" name="meta[1]" />
				</form>
			')
			->seeForm('myForm')
				->dontSeeArrayInput('meta', 2);
	}

	/** @test */
	public function it_throws_if_an_array_input_is_found_within_a_form()
	{
		$this->setExpectedException('\Exception', 'Element input[name="meta[1]"] was found');

		$this->instance
			->setResponse('
				<form id="myForm">
					<input type="text" name="meta[1]" />
				</form>
			')
			->seeForm('myForm')
				->dontSeeArrayInput('meta', 1);
	}

	/** @test */
	public function it_asserts_that_a_nested_array_input_is_found()
	{
		$this->instance
			->setResponse('
				<form id="myForm">
					<input type="text" name="filter[0][field]" />
				</form>
			')
			->seeForm('myForm')
				->seeInput('filter[0][field]');
	}

	/** @test */
	public function it_asserts_that_a_nested_array_input_is_not_found()
	{
		$this->setExpectedException(\Exception::class, 'Could not find input[name="filter[1][field]"]');
		
		$this->instance
			->setResponse('
				<form id="myForm">
					<input type="text" name="filter[0][field]" />
				</form>
			')
			->seeForm('myForm')
				->seeInput('filter[1][field]');
	}

	/** @test */
	public function it_asserts_that_a_select_is_found_within_a_form()
	{
		$this->instance
			->setResponse('
				<form id="myForm">
					<select name="clientArea"></select>
				</form>
			')
			->seeForm('myForm')
				->seeSelect('clientArea');
	}

	/** @test */
	public function it_throws_if_a_select_is_not_found_within_a_form()
	{
		$this->setExpectedException('\Exception', 'Could not find select[name="clientArea"]');

		$this->instance
			->setResponse('
				<form id="myForm"></form>
			')
			->seeForm('myForm')
				->seeSelect('clientArea');
	}

	/** @test */
	public function it_asserts_that_a_select_is_not_found_within_a_form()
	{
		$this->instance
			->setResponse('
				<form id="myForm">
					<select name="clientArea"></select>
				</form>
			')
			->seeForm('myForm')
				->dontSeeSelect('clientLabels');
	}

	/** @test */
	public function it_throws_if_select_is_found_within_a_form()
	{
		$this->setExpectedException('\Exception', 'Element select[name="clientArea"] was found');

		$this->instance
			->setResponse('
				<form id="myForm">
					<select name="clientArea"></select>
				</form>
			')
			->seeForm('myForm')
				->dontSeeSelect('clientArea');
	}

	/** @test */
	public function it_asserts_select_has_options_with_values()
	{
		$this->instance
			->setResponse('
				<form id="myForm">
					<select name="clientArea">
						<option value="it_IT">Italian</option>
						<option value="es_ES">Spanish</option>
						<option value="de_DE">Deutsch</option>
					</select>
				</form>
			')
			->seeForm('myForm')
				->seeSelect('clientArea')
					->withOptions(['it_IT', 'es_ES', 'de_DE']);
	}

	/** @test */
	public function it_throws_if_select_has_not_options_with_values()
	{
		$this->setExpectedException('\Exception', 'Could not find any option with value [es_ES]');

		$this->instance
			->setResponse('
				<form id="myForm">
					<select name="clientArea">
						<option value="it_IT">Italian</option>
						<option value="de_DE">Deutsch</option>
					</select>
				</form>
			')
			->seeForm('myForm')
				->seeSelect('clientArea')
					->withOptions(['it_IT', 'es_ES', 'de_DE']);
	}

	/** @test */
	public function it_asserts_select_has_not_options_with_values()
	{
		$this->instance
			->setResponse('
				<form id="myForm">
					<select name="clientArea">
						<option value="it_IT">Italian</option>
						<option value="es_ES">Spanish</option>
						<option value="de_DE">Deutsch</option>
					</select>
				</form>
			')
			->seeForm('myForm')
				->seeSelect('clientArea')
					->withoutOptions(['pt_PT', 'au_AU']);
	}

	/** @test */
	public function it_throws_if_select_has_options_with_values()
	{
		$this->setExpectedException('\Exception', 'An option with value [it_IT] was found');

		$this->instance
			->setResponse('
				<form id="myForm">
					<select name="clientArea">
						<option value="it_IT">Italian</option>
						<option value="es_ES">Spanish</option>
						<option value="de_DE">Deutsch</option>
					</select>
				</form>
			')
			->seeForm('myForm')
				->seeSelect('clientArea')
					->withoutOptions(['pt_PT', 'it_IT', 'au_AU']);
	}

	/** @test */
	public function it_asserts_textarea_is_found_within_a_form()
	{
		$this->instance
			->setResponse('
				<form id="myForm">
					<textarea name="note"></textarea>
				</form>
			')
			->seeForm('myForm')
				->seeTextarea('note');
	}

	/** @test */
	public function it_throws_if_textarea_is_not_found_within_a_form()
	{
		$this->setExpectedException('\Exception', 'Could not find textarea[name="bar"]');

		$this->instance
			->setResponse('
				<form id="myForm">
					<textarea name="foo"></textarea>
				</form>
			')
			->seeForm('myForm')
				->seeTextarea('bar');
	}

	/** @test */
	public function it_asserts_textarea_is_not_found_within_a_form()
	{
		$this->instance
			->setResponse('
				<form id="myForm">
					<textarea name="foo"></textarea>
				</form>
			')
			->seeForm('myForm')
				->dontSeeTextarea('bar');
	}

	/** @test */
	public function it_throws_if_textarea_is_found_within_a_form()
	{
		$this->setExpectedException('\Exception', 'Element textarea[name="foo"] was found');

		$this->instance
			->setResponse('
				<form id="myForm">
					<textarea name="foo"></textarea>
				</form>
			')
			->seeForm('myForm')
				->dontSeeTextarea('foo');
	}

	/** @test */
	public function it_asserts_checkbox_is_found_within_a_form()
	{
		$this->instance
			->setResponse('
				<form id="myForm">
					<input type="checkbox" name="agree" value="1" />
				</form>
			')
			->seeForm('myForm')
				->seeCheckbox('agree');
	}

	/** @test */
	public function it_throws_if_checkbox_is_not_found_within_a_form()
	{
		$this->setExpectedException('\Exception', 'Could not find input[type="checkbox"][name="foo"]');

		$this->instance
			->setResponse('
				<form id="myForm">
					<input type="checkbox" name="bar" value="1" />
				</form>
			')
			->seeForm('myForm')
				->seeCheckbox('foo');
	}

	/** @test */
	public function it_asserts_checkbox_is_not_found_within_a_form()
	{
		$this->instance
			->setResponse('
				<form id="myForm">
					<input type="checkbox" name="foo" value="1" />
				</form>
			')
			->seeForm('myForm')
				->dontSeeCheckbox('bar');
	}

	/** @test */
	public function it_throws_if_checkbox_is_found_within_a_form()
	{
		$this->setExpectedException('\Exception', 'Element input[type="checkbox"][name="foo"] was found');

		$this->instance
			->setResponse('
				<form id="myForm">
					<input type="checkbox" name="foo" value="1" />
				</form>
			')
			->seeForm('myForm')
				->dontSeeCheckbox('foo');
	}

	/** @test */
	public function it_asserts_input_has_expected_value()
	{
		$this->instance
			->setResponse('
				<form id="myForm">
					<input name="foo" value="Foo" />
				</form>
			')
			->seeForm('myForm')
				->seeInput('foo')
					->withValue('Foo');
	}

	/** @test */
	public function it_throws_if_input_has_not_expected_value()
	{
		$this->setExpectedException('\Exception', 'Could not assert that value [Bar] equals to [Foo]');

		$this->instance
			->setResponse('
				<form id="myForm">
					<input name="foo" value="Foo" />
				</form>
			')
			->seeForm('myForm')
				->seeInput('foo')
					->withValue('Bar');
	}

	/** @test */
	public function it_asserts_that_a_select_has_expected_value()
	{
		$this->instance
			->setResponse('
				<form id="myForm">
					<select name="language">
						<option value="it_IT">Italian</option>
						<option value="de_DE" selected="selected">German</option>
						<option value="es_ES">Spanish</option>
					</select>
				</form>
			')
			->seeForm('myForm')
				->seeSelect('language')
					->withValue('de_DE');
	}

	/** @test */
	public function it_throws_if_select_has_not_expected_value()
	{
		$this->setExpectedException('\Exception', 'Could not assert that value [it_IT] equals to [de_DE]');

		$this->instance
			->setResponse('
				<form id="myForm">
					<select name="language">
						<option value="it_IT">Italian</option>
						<option value="de_DE" selected="selected">German</option>
						<option value="es_ES">Spanish</option>
					</select>
				</form>
			')
			->seeForm('myForm')
				->seeSelect('language')
					->withValue('it_IT');
	}

	/** @test */
	public function it_asserts_that_textarea_has_expected_value()
	{
		$this->instance
			->setResponse('
				<form id="myForm">
					<textarea name="note">Lorem ipsum</textarea>
				</form>
			')
			->seeForm('myForm')
				->seeTextarea('note')
					->withValue('Lorem ipsum');
	}

	/** @test */
	public function it_throws_if_textarea_has_not_expected_value()
	{
		$this->setExpectedException('\Exception', 'Could not assert that value [Dolor sit amet] equals to [Lorem ipsum]');

		$this->instance
			->setResponse('
				<form id="myForm">
					<textarea name="note">Lorem ipsum</textarea>
				</form>
			')
			->seeForm('myForm')
				->seeTextarea('note')
					->withValue('Dolor sit amet');
	}

	/** @test */
	public function it_asserts_checkbox_has_expected_value()
	{
		$this->instance
			->setResponse('
				<form id="myForm">
					<input type="checkbox" name="agreenment" value="agree" checked="checked" />
				</form>
			')
			->seeForm('myForm')
				->seeCheckbox('agreenment')
					->withValue('agree');
	}

	/** @test */
	public function it_throws_if_checkbox_has_not_expected_value()
	{
		$this->setExpectedException('\Exception', 'Could not assert that value [bar] equals to [foo]');

		$this->instance
			->setResponse('
				<form id="myForm">
					<input type="checkbox" name="agree" value="foo" checked="checked" />
				</form>
			')
			->seeForm('myForm')
				->seeCheckbox('agree')
					->withValue('bar');
	}

	/** @test */
	public function it_asserts_a_form_has_fields_filled_with_values()
	{
		$this->instance
			->setResponse('
				<form id="myForm" action="index.php?cmd=Foo">
					<input name="firstName" value="Jason" />
					<input name="lastName" value="Bourne" />
					<input name="jobTitle" value="Secret Agent" />
				</form>
			')
			->seeForm('myForm')
			->filledWith([
				'firstName' => 'Jason',
				'lastName' => 'Bourne',
				'jobTitle' => 'Secret Agent',
			]);
	}

	/** @test */
	public function it_throws_if_a_form_has_not_fields_filled_with_values()
	{
		$this->setExpectedException('\Exception', 'Could not assert that value [George] equals to [Jason]');

		$this->instance
			->setResponse('
				<form id="myForm" action="index.php?cmd=Foo">
					<input name="firstName" value="Jason" />
					<input name="lastName" value="Bourne" />
					<input name="jobTitle" value="Secret Agent" />
				</form>
			')
			->seeForm('myForm')
			->filledWith([
				'firstName' => 'George',
				'lastName' => 'Doe',
				'jobTitle' => 'Coder',
			]);
	}
}