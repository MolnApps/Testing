<?php

use \MolnApps\Testing\TestCase;

use \MolnApps\Testing\Response\FormInspector;
use \MolnApps\Testing\Response\Html\DomNode;

class FormInspectorTest extends TestCase
{
	private $document;
	private $inspector;

	protected function setUp() : void
	{
		$form = $this->createFormElement();

		$this->inspector = new FormInspector($form);
	}
	
	/** @test */
	public function it_can_be_instantiated()
	{
		$this->assertNotNull($this->inspector);
	}

	// ! Input

	/** @test */
	public function it_asserts_input_is_found()
	{
		$this->inspector->seeInput('firstName');
	}

	/** @test */
	public function it_throws_if_input_is_not_found()
	{
		$this->setExpectedException(
			\Exception::class, 
			'Could not find input[name="foobar"]'
		);

		$this->inspector->seeInput('foobar');
	}

	/** @test */
	public function it_asserts_input_is_not_found()
	{
		$this->inspector->dontSeeInput('foobar');
	}

	/** @test */
	public function it_throws_if_input_is_found()
	{
		$this->setExpectedException(
			\Exception::class, 
			'Element input[name="firstName"] was found'
		);

		$this->inspector->dontSeeInput('firstName');
	}

	/** @test */
	public function it_asserts_input_is_found_with_value()
	{
		$this->inspector->seeInput('firstName')->withValue('George');
	}

	/** @test */
	public function it_throws_if_input_is_not_found_with_value()
	{
		$this->setExpectedException(
			\Exception::class, 
			'Could not assert that value [Foobar] equals to [George]'
		);
		$this->inspector->seeInput('firstName')->withValue('Foobar');
	}

	/** @test */
	public function it_enters_input_value()
	{
		$this->inspector->seeInput('firstName')->enter('John Doe');
		$this->inspector->seeInput('firstName')->withValue('John Doe');
		$this->assertStringContainsString(
			'<input type="text" name="firstName" value="John Doe">', 
			$this->document->saveHtml()
		);
	}

	// ! Array input

	/** @test */
	public function it_asserts_an_array_input_is_found()
	{
		$this->inspector->seeArrayInput('meta', 1);
	}

	/** @test */
	public function it_throws_if_array_input_is_not_found()
	{
		$this->setExpectedException(
			\Exception::class, 
			'Could not find input[name="meta[2]"]'
		);
		$this->inspector->seeArrayInput('meta', 2);
	}

	/** @test */
	public function it_asserts_an_array_input_is_not_found()
	{
		$this->inspector->dontSeeArrayInput('meta', 2);
	}

	/** @test */
	public function it_throws_if_array_input_is_found()
	{
		$this->setExpectedException(
			\Exception::class, 
			'Element input[name="meta[1]"] was found'
		);
		$this->inspector->dontSeeArrayInput('meta', 1);
	}

	// ! Textarea

	/** @test */
	public function it_asserts_textarea_is_found()
	{
		$this->inspector->seeTextarea('message');
	}

	/** @test */
	public function it_throws_if_textarea_is_not_found()
	{
		$this->setExpectedException(
			\Exception::class, 
			'Could not find textarea[name="foobar"]'
		);

		$this->inspector->seeTextarea('foobar');
	}

	/** @test */
	public function it_asserts_textarea_is_not_found()
	{
		$this->inspector->dontSeeTextarea('foobar');
	}

	/** @test */
	public function it_throws_if_textarea_is_found()
	{
		$this->setExpectedException(
			\Exception::class, 
			'Element textarea[name="message"] was found'
		);

		$this->inspector->dontSeeTextarea('message');
	}

	/** @test */
	public function it_asserts_textarea_is_found_with_value()
	{
		$this->inspector->seeTextarea('message')->withValue('Lorem ipsum dolor sit amet');
	}

	/** @test */
	public function it_throws_if_textarea_is_not_found_with_value()
	{
		$this->setExpectedException(
			\Exception::class, 
			'Could not assert that value [Foobar] equals to [Lorem ipsum dolor sit amet]'
		);
		$this->inspector->seeTextarea('message')->withValue('Foobar');
	}

	/** @test */
	public function it_enters_textarea_value()
	{
		$this->inspector->seeTextarea('message')->enter('Hello bold world');
		$this->inspector->seeTextarea('message')->withValue('Hello bold world');
		$this->assertStringContainsString(
			'<textarea name="message">Hello bold world</textarea>', 
			$this->document->saveHtml()
		);
	}

	// ! Select

	/** @test */
	public function it_asserts_select_is_found()
	{
		$this->inspector->seeSelect('country');
	}

	/** @test */
	public function it_throws_if_select_is_not_found()
	{
		$this->setExpectedException(
			\Exception::class, 
			'Could not find select[name="foobar"]'
		);

		$this->inspector->seeSelect('foobar');
	}

	/** @test */
	public function it_asserts_select_is_not_found()
	{
		$this->inspector->dontSeeSelect('foobar');
	}

	/** @test */
	public function it_throws_if_select_is_found()
	{
		$this->setExpectedException(
			\Exception::class, 
			'Element select[name="country"] was found'
		);

		$this->inspector->dontSeeSelect('country');
	}

	/** @test */
	public function it_asserts_select_is_found_with_value()
	{
		$this->inspector->seeSelect('country')->withValue('it_IT');
	}

	/** @test */
	public function it_asserts_select_is_found_with_numeric_value()
	{
		$this->inspector->seeSelect('status')->withValue(1);
		$this->inspector->seeSelect('status')->withValue('1');
	}

	/** @test */
	public function it_asserts_select_is_found_with_options()
	{
		$this->inspector->seeSelect('country')->withOptions(['de_DE', 'en_US', 'it_IT']);
	}

	/** @test */
	public function it_asserts_select_is_found_without_options()
	{
		$this->inspector->seeSelect('country')->withoutOptions(['jp_JP']);
	}

	/** @test */
	public function it_throws_if_select_is_not_found_with_value()
	{
		$this->setExpectedException(
			\Exception::class, 
			'Could not assert that value [Foobar] equals to [it_IT]'
		);
		$this->inspector->seeSelect('country')->withValue('Foobar');
	}

	/** @test */
	public function it_throws_if_select_is_found_without_options()
	{
		$this->setExpectedException(
			\Exception::class, 
			'Could not find any option with value [jp_JP]'
		);
		$this->inspector->seeSelect('country')->withOptions(['de_DE', 'jp_JP', 'it_IT']);
	}

	/** @test */
	public function it_throws_if_select_is_not_found_without_options()
	{
		$this->setExpectedException(
			\Exception::class, 
			'An option with value [de_DE] was found'
		);
		$this->inspector->seeSelect('country')->withoutOptions(['de_DE', 'en_US', 'it_IT']);
	}

	/** @test */
	public function it_enters_select_value()
	{
		$this->inspector->seeSelect('country')->choose('de_DE');
		$this->inspector->seeSelect('country')->withValue('de_DE');
		$this->assertStringContainsString(
			'<option value="de_DE" selected>Deutsch</option>', 
			$this->document->saveHtml()
		);
		$this->assertStringNotContainsString(
			'<option value="it_IT" selected>Italiano</option>', 
			$this->document->saveHtml()
		);
	}

	// ! Multiple Select

	/** @test */
	public function it_asserts_multiple_select_is_found()
	{
		$this->inspector->seeSelect('labels[]');
	}

	/** @test */
	public function it_throws_if_multiple_select_is_found()
	{
		$this->setExpectedException(
			\Exception::class, 
			'Element select[name="labels[]"] was found'
		);

		$this->inspector->dontSeeSelect('labels[]');
	}

	/** @test */
	public function it_asserts_multiple_select_is_found_with_value()
	{
		$this->inspector->seeSelect('labels[]')->withValue([1, 3, 5]);
	}

	/** @test */
	public function it_asserts_multiple_select_is_found_with_options()
	{
		$this->inspector->seeSelect('labels[]')->withOptions([1, 2, 3, 4, 5]);
	}

	/** @test */
	public function it_asserts_multiple_select_is_found_without_options()
	{
		$this->inspector->seeSelect('labels[]')->withoutOptions([6, 7, 8]);
	}

	/** @test */
	public function it_throws_if_multiple_select_is_not_found_with_value()
	{
		$this->setExpectedException(
			\Exception::class, 
			'Could not assert that value [Foobar] was found in [1, 3, 5]'
		);
		$this->inspector->seeSelect('labels[]')->withValue('Foobar');
	}

	/** @test */
	public function it_throws_if_multiple_select_is_found_without_options()
	{
		$this->setExpectedException(
			\Exception::class, 
			'Could not find any option with value [6]'
		);
		$this->inspector->seeSelect('labels[]')->withOptions([1, 2, 3, 4, 6, 7]);
	}

	/** @test */
	public function it_throws_if_multiple_select_is_not_found_without_options()
	{
		$this->setExpectedException(
			\Exception::class, 
			'An option with value [3] was found'
		);
		$this->inspector->seeSelect('labels[]')->withoutOptions(['6', '3', '7']);
	}

	/** @test */
	public function it_enters_multiple_select_value()
	{
		$this->inspector->seeSelect('labels[]')->choose([2, 4]);
		$this->inspector->seeSelect('labels[]')->withValue([2, 4]);
		
		$this->assertStringContainsString(
			'<option value="2" selected>Ipsum</option>', 
			$this->document->saveHtml()
		);
		$this->assertStringContainsString(
			'<option value="4" selected>Sit</option>', 
			$this->document->saveHtml()
		);
		
		$this->assertStringNotContainsString(
			'<option value="1" selected>Lorem</option>', 
			$this->document->saveHtml()
		);
		$this->assertStringNotContainsString(
			'<option value="3" selected>Dolor</option>', 
			$this->document->saveHtml()
		);
		$this->assertStringNotContainsString(
			'<option value="4" selected>Amet</option>', 
			$this->document->saveHtml()
		);
	}

	// ! Checkbox

	/** @test */
	public function it_asserts_checkbox_is_found()
	{
		$this->inspector->seeCheckbox('agree');
	}

	/** @test */
	public function it_throws_if_checkbox_is_not_found()
	{
		$this->setExpectedException(
			\Exception::class, 
			'Could not find input[type="checkbox"][name="foobar"]'
		);

		$this->inspector->seeCheckbox('foobar');
	}

	/** @test */
	public function it_throws_if_input_that_is_not_checkbox_is_found()
	{
		$this->setExpectedException(
			\Exception::class, 
			'Could not find input[type="checkbox"][name="firstName"]'
		);
		
		$this->inspector->seeCheckbox('firstName');
	}

	/** @test */
	public function it_asserts_checkbox_is_not_found()
	{
		$this->inspector->dontSeeCheckbox('foobar');
	}

	/** @test */
	public function it_throws_if_checkbox_is_found()
	{
		$this->setExpectedException(
			\Exception::class, 
			'Element input[type="checkbox"][name="agree"] was found'
		);

		$this->inspector->dontSeeCheckbox('agree');
	}

	/** @test */
	public function it_does_not_throw_if_input_that_is_not_checkbox_is_found()
	{
		$this->inspector->dontSeeCheckbox('firstName');
	}

	/** @test */
	public function it_asserts_checkbox_is_checked()
	{
		$this->inspector->seeCheckbox('agree')->withValue('agreenment');
	}

	/** @test */
	public function it_asserts_checkbox_is_not_checked()
	{
		$this->inspector->seeCheckbox('subscribe')->withValue('');
	}

	/** @test */
	public function it_throws_if_checkbox_has_not_value()
	{
		$this->setExpectedException(
			\Exception::class, 
			'Could not assert that value [foobar] equals to [agreenment]'
		);
		$this->inspector->seeCheckbox('agree')->withValue('foobar');
	}

	/** @test */
	public function it_checks_a_checkbox()
	{
		$this->inspector->seeCheckbox('subscribe')->withValue('');
		
		$this->inspector->seeCheckbox('subscribe')->check();

		$this->inspector->seeCheckbox('subscribe')->withValue('subscription');
		$this->assertStringContainsString(
			'<input type="checkbox" name="subscribe" value="subscription" checked>', 
			$this->document->saveHtml()
		);
	}

	/** @test */
	public function it_unchecks_a_checkbox()
	{
		$this->inspector->seeCheckbox('agree')->withValue('agreenment');
		
		$this->inspector->seeCheckbox('agree')->uncheck();
		
		$this->inspector->seeCheckbox('agree')->withValue('');
		$this->assertStringContainsString(
			'<input type="checkbox" name="agree" value="agreenment">', 
			$this->document->saveHtml()
		);
	}

	// ! File input
	
	/** @test */
	public function it_asserts_file_is_found()
	{
		$this->inspector->seeFile('myUpload');
	}

	/** @test */
	public function it_throws_if_file_is_not_found()
	{
		$this->setExpectedException(
			\Exception::class, 
			'Could not find input[type="file"][name="foobar"]'
		);

		$this->inspector->seeFile('foobar');
	}

	/** @test */
	public function it_throws_if_input_that_is_not_file_is_found()
	{
		$this->setExpectedException(
			\Exception::class, 
			'Could not find input[type="file"][name="firstName"]'
		);

		$this->inspector->seeFile('firstName');
	}

	/** @test */
	public function it_asserts_file_is_not_found()
	{
		$this->inspector->dontSeeFile('foobar');
	}

	/** @test */
	public function it_throws_if_file_is_found()
	{
		$this->setExpectedException(
			\Exception::class, 
			'Element input[type="file"][name="myUpload"] was found'
		);

		$this->inspector->dontSeeFile('myUpload');
	}

	/** @test */
	public function it_does_not_throw_if_input_that_is_not_file_is_found()
	{
		$this->inspector->dontSeeFile('firstName');
	}

	/** @test */
	public function it_sets_an_uploaded_file_array_as_a_value()
	{
		$array = [
			'tmp_name' => '/path/to/image.png',
			'name' => 'image.png',
			'type' => 'image/png',
			'size' => 1024,
			'error' => 0,
		];

		$this->inspector->seeFile('myUpload')->enter($array);

		$this->inspector->seeFile('myUpload')->withValue($array);
	}

	private function createFormElement()
	{
		$this->document = new \DomDocument;
		$this->document->loadHtml('
			<form>
				<input type="text" name="firstName" value="George" />
				<input type="hidden" name="meta[1]" />
				<textarea name="message">Lorem ipsum dolor sit amet</textarea>
				<select name="country">
					<option value="de_DE">Deutsch</option>
					<option value="en_US">English</option>
					<option value="it_IT" selected="selected">Italiano</option>
				</select>
				<select name="status">
					<option value="1" selected="selected">Active</option>
					<option value="0">Blocked</option>
				</select>
				<select name="labels[]" multiple>
					<option value="1" selected="selected">Lorem</option>
					<option value="2">Ipsum</option>
					<option value="3" selected="selected">Dolor</option>
					<option value="4">Sit</option>
					<option value="5" selected="selected">Amet</option>
				</select>
				<input type="checkbox" name="agree" value="agreenment" checked="checked" />
				<input type="checkbox" name="subscribe" value="subscription" />
				<input type="file" name="myUpload" />
			</form>
		');
		
		$form = $this->document->getElementsByTagName('form')[0];
		
		return new DomNode($form);
	}
}