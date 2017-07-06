<?php

namespace MolnApps\Testing\Response;

use \MolnApps\Testing\Response\Testing\ControllerStub;
use \MolnApps\Testing\Router\Router;

class ResponseInspectorElementTest extends \PHPUnit_Framework_TestCase
{
	protected function setUp()
	{
		$this->instance = new ResponseInspector(new ControllerStub, new Router);
	}

	/** @test */
	public function it_asserts_that_a_tag_with_a_class_is_found()
	{
		$this->instance
			->setResponse('<p class="foo bar baz">Lorem ipsum dolor sit amet</p>')
			->seeElement('p.bar');
	}

	/** @test */
	public function it_throws_if_a_tag_with_a_class_is_not_found()
	{
		$message = 'No element [p.baz] was found';
		$message = 'Element p.baz was expected at least 1 time';
		$this->setExpectedException('\Exception', $message);

		$this->instance
			->setResponse('<p class="foo bar">Lorem ipsum dolor sit amet</p>')
			->seeElement('p.baz');
	}

	/** @test */
	public function it_asserts_that_a_tag_with_multiple_classes_is_found()
	{
		$this->instance
			->setResponse('<p class="foo bar baz">Lorem ipsum dolor sit amet</p>')
			->seeElement('p.foo.baz.bar');
	}

	/** @test */
	public function it_throws_if_a_tag_with_multiple_classes_is_not_found()
	{
		$message = 'No element [p.foo.baz.bar] was found';
		$message = 'Element p.foo.baz.bar was expected at least 1 time';
		
		$this->setExpectedException('\Exception', $message);

		$this->instance
			->setResponse('<p class="foo bar">Lorem ipsum dolor sit amet</p>')
			->seeElement('p.foo.baz.bar');
	}

	/** @test */
	public function it_asserts_that_a_tag_with_capitalized_classes_is_found()
	{
		$this->instance
			->setResponse('<div class="Report Item">Hello world</div>')
			->seeElement('div.Report.Item');
	}

	/** @test */
	public function it_asserts_that_a_nested_tag_with_capitalized_classes_is_found()
	{
		$this->instance
			->setResponse('
				<div class="contents">
					<p class="container">
						<div class="Report Item">Hello world</div>
						<div class="Report Item">Dolor sit</div>
						<div class="Report Item">Amet consectetur</div>
					</p>
				</div>	
			')
			->seeElement('div.Report.Item');
	}

	/** @test */
	public function it_asserts_that_a_tag_with_class_contains_text()
	{
		$this->instance
			->setResponse('<p class="foo bar baz">Lorem ipsum dolor sit amet</p>')
			->seeElement('p.foo')
			->withAttribute('text', 'Lorem ipsum dolor');
	}

	/** @test */
	public function it_throws_if_a_tag_with_class_does_not_contain_text()
	{
		$message = 'No element [p.foo] was found with attributes [text = "Foobar"]';
		$message = 'Element p.foo[text="Foobar"] was expected at least 1 time';
		$this->setExpectedException('\Exception', $message);

		$this->instance
			->setResponse('<p class="foo bar baz">Lorem ipsum dolor sit amet</p>')
			->seeElement('p.foo')
			->withAttribute('text', 'Foobar');
	}

	/** @test */
	public function it_asserts_that_a_tag_contains_text()
	{
		$this->instance
			->setResponse('
				<h3>Hello</h3>
				<p>Lorem ipsum dolor sit amet</p>
				<h3>World</h3>
				<p>Consectetur adipiscing elit</p>
			')
			->seeElement('h3')
			->withAttribute('text', 'World');
	}

	/** @test */
	public function it_throws_if_a_tag_does_not_contain_text()
	{
		$message = 'No element [h3] was found with attributes [text = "Foobar"]';
		$message = 'Element h3[text="Foobar"] was expected at least 1 time';
		$this->setExpectedException('\Exception', $message);
		
		$this->instance
			->setResponse('
				<h3>Hello</h3>
				<p>Lorem ipsum dolor sit amet</p>
				<h3>World</h3>
				<p>Consectetur adipiscing elit</p>
			')
			->seeElement('h3')
			->withAttribute('text', 'Foobar');
	}

	/** @test */
	public function it_asserts_that_multiple_tags_contain_text()
	{
		$this->instance
			->setResponse('
				<h3>Hello</h3>
				<p>Lorem ipsum dolor sit amet</p>
				<h3>World</h3>
				<p>Consectetur adipiscing elit</p>
			')
			->seeElement('h3')->withAttribute('text', 'World')
			->seeElement('h3')->withAttribute('text', 'Hello');
	}

	/** @test */
	public function it_asserts_that_a_tag_with_multiple_classes_contains_text()
	{
		$this->instance
			->setResponse('
				<h3>Hello</h3>
				<p class="feedback error">Lorem ipsum dolor sit amet</p>
				<h3>World</h3>
				<p class="feedback">Consectetur adipiscing elit</p>
			')
			->seeElement('p.feedback.error')->withAttribute('text', 'Lorem ipsum dolor sit amet');
	}

	/** @test */
	public function it_asserts_that_a_tag_contains_text_on_multiple_lines()
	{
		$this->instance
			->setResponse('
				<p class="address">Via Mazzini 35<br/>20100 Bresso (MI), Italy</p>
			')
			->seeElement('p.address')
			->withAttribute(
				'text', 
				'Via Mazzini 35'."\r\n".
				'20100 Bresso (MI), Italy'
			);
	}

	/** @test */
	public function it_asserts_that_a_tag_contains_text_on_multiple_lines_and_mixed_formatting()
	{
		$this->instance
			->setResponse('
				<p class="address">
					Via Mazzini 35<br/>
					20100 Bresso (MI), 
					Italy
				</p>
			')
			->seeElement('p.address')
			->withAttribute(
				'text', 
				'Via Mazzini 35 20100 Bresso (MI), Italy'
			);
	}

	/** @test */
	public function it_asserts_that_a_tag_contains_text_with_special_characters()
	{
		$this->instance
			->setResponse('
				<p class="address">
					'.htmlentities("Mr. Simon O'Connor", ENT_QUOTES, 'UTF-8').'
					Chief Financial Officer
				</p>
			')
			->seeElement('p.address')
			->withAttribute('text', "Simon O'Connor");
	}

	/** @test */
	public function it_asserts_that_it_finds_exactly_n_elements_matching_the_selector()
	{
		$this->instance
			->setResponse('
				<div class="contents">
					<div class="Report Item">
						Lorem ipsum
					</div>
					<div class="Report Item">
						Dolor sit amet
					</div>
					<div class="Report Item">
						Consectetur adipiscing
					</div>
				</div>
			')
			->seeElement('div.Report.Item')
			->times(3);
	}

	/** @test */
	public function it_throws_if_does_not_find_exactly_n_elements_matching_the_selector()
	{
		$message = 'Element [div.Report.Item] was expected 4 times but was found 3 times';
		$message = 'Element div.Report.Item was expected 4 times but was found 3 times';
		$this->setExpectedException('\Exception', $message);
		
		$this->instance
			->setResponse('
				<div class="contents">
					<div class="Report Item">
						Lorem ipsum
					</div>
					<div class="Report Item">
						Dolor sit amet
					</div>
					<div class="Report Item">
						Consectetur adipiscing
					</div>
				</div>
			')
			->seeElement('div.Report.Item')
			->times(4);
	}

	/** @test */
	public function it_asserts_that_multiple_items_with_attributes_are_found()
	{
		$this->instance
			->setResponse('
				<div class="contents">
					<div class="Report Item">
						Lorem ipsum
					</div>
					<div class="Report Item">
						Dolor sit amet
					</div>
					<div class="Report Item">
						Consectetur adipiscing
					</div>
				</div>
			')
			->seeElement('div.Report.Item')
				->withAttribute('text', 'Lorem ipsum')
				->withAttribute('text', 'Dolor sit amet')
				->withAttribute('text', 'Consectetur adipiscing');
	}

	/** @test */
	public function it_asserts_that_a_ordered_list_of_elements_is_found()
	{
		$this->instance
			->setResponse('
				<div class="contents">
					<div class="Report Item">
						Lorem ipsum
					</div>
					<div class="Report Item">
						Dolor sit amet
					</div>
					<div class="Report Item">
						Consectetur adipiscing
					</div>
				</div>
			')
			->seeElement('div.Report.Item')
				->ordered()
				->withText('Lorem ipsum')
				->withText('Dolor sit amet')
				->withText('Consectetur adipiscing');
	}

	/** @test */
	public function it_throws_if_a_ordered_list_of_elements_is_not_found()
	{
		$message = 'No element [div.Report.Item] was found with attributes [text = "Consectetur adipiscing"] at index [1]';
		$message = 'Element div.Report.Item[text="Consectetur adipiscing"] was expected at index 1';
		$this->setExpectedException('\Exception', $message);

		$this->instance
			->setResponse('
				<div class="contents">
					<div class="Report Item">
						Lorem ipsum
					</div>
					<div class="Report Item">
						Dolor sit amet
					</div>
					<div class="Report Item">
						Consectetur adipiscing
					</div>
				</div>
			')
			->seeElement('div.Report.Item')
				->ordered()
				->withText('Lorem ipsum')
				->withText('Consectetur adipiscing')
				->withText('Dolor sit amet');
	}

	/** @test */
	public function it_finds_an_element_by_its_id()
	{
		$this->instance
			->setResponse('
				<div class="contents">
					<div id="loremIpsum" class="Report Item">
						Lorem ipsum
					</div>
					<div id="dolorSitAmet" class="Report Item">
						Dolor sit amet
					</div>
					<div id="consectetur" class="Report Item">
						Consectetur adipiscing
					</div>
				</div>
			')
			->seeElement('div')
				->withAttribute('id', 'dolorSitAmet');
	}

	/** @test */
	public function it_throws_if_could_not_find_an_element_by_its_id()
	{
		$message = 'No element [div] was found with attributes [id = "dolorSitAmet1"]';
		$message = 'Element div#dolorSitAmet1 was expected at least 1 time';
		$this->setExpectedException('\Exception', $message);
		
		$this->instance
			->setResponse('
				<div class="contents">
					<div id="loremIpsum" class="Report Item">
						Lorem ipsum
					</div>
					<div id="dolorSitAmet" class="Report Item">
						Dolor sit amet
					</div>
					<div id="consectetur" class="Report Item">
						Consectetur adipiscing
					</div>
				</div>
			')
			->seeElement('div')
				->withAttribute('id', 'dolorSitAmet1');
	}

	/** @test */
	public function it_asserts_that_an_item_with_multiple_attributes_is_found()
	{
		$this->instance
			->setResponse('
				<div class="contents">
					<div id="loremIpsum" class="Report Item">
						Lorem ipsum
					</div>
					<div id="dolorSitAmet" class="Report Item">
						Dolor sit amet
					</div>
					<div id="consectetur" class="Report Item">
						Consectetur adipiscing
					</div>
				</div>
			')
			->seeElement('div')
				->withAttributes([
					'id' =>'dolorSitAmet', 
					'class' => 'Report Item', 
					'text' => 'Dolor sit'
				]);
	}

	/** @test */
	public function it_throws_if_an_item_with_multiple_attributes_is_not_found()
	{
		$message = 'No element [div] was found with attributes [id = "dolorSitAmet"] and [class = "Report Item Foobar"] and [text = "Dolor sit"]';
		$message = 'Element div#dolorSitAmet.Report.Item.Foobar[text="Dolor sit"] was expected at least 1 time';
		$this->setExpectedException('\Exception', $message);

		$this->instance
			->setResponse('
				<div class="contents">
					<div id="loremIpsum" class="Report Item">
						Lorem ipsum
					</div>
					<div id="dolorSitAmet" class="Report Item">
						Dolor sit amet
					</div>
					<div id="consectetur" class="Report Item">
						Consectetur adipiscing
					</div>
				</div>
			')
			->seeElement('div')
				->withAttributes([
					'id' =>'dolorSitAmet', 
					'class' => 'Report Item Foobar', 
					'text' => 'Dolor sit'
				]);
	}

	/** @test */
	public function it_asserts_that_multiple_items_with_multiple_attributes_are_found_in_a_given_order()
	{
		$this->instance
			->setResponse('
				<div class="contents">
					<div id="loremIpsum" class="Report Item">
						Lorem ipsum
					</div>
					<div id="dolorSitAmet" class="Report Item">
						Dolor sit amet
					</div>
					<div id="consectetur" class="Report Item">
						Consectetur adipiscing
					</div>
				</div>
			')
			->seeElement('div.Report.Item')
				->ordered()
				->withAttributes([
					'id' =>'loremIpsum', 
					'text' => 'Lorem ipsum'
				])
				->withAttributes([
					'id' =>'dolorSitAmet', 
					'text' => 'Dolor sit amet'
				])
				->withAttributes([
					'id' =>'consectetur', 
					'text' => 'Consectetur adipiscing'
				]);
	}

	/** @test */
	public function it_throws_if_multiple_items_with_multiple_attributes_are_not_found_in_a_given_order()
	{
		$message = 'No element [div.Report.Item] was found with attributes [id = "consectetur"] and [text = "Consectetur adipiscing"] at index [1]';
		$message = 'Element div#consectetur.Report.Item[text="Consectetur adipiscing"] was expected at index 1';
		$this->setExpectedException('\Exception', $message);
		
		$this->instance
			->setResponse('
				<div class="contents">
					<div id="loremIpsum" class="Report Item">
						Lorem ipsum
					</div>
					<div id="dolorSitAmet" class="Report Item">
						Dolor sit amet
					</div>
					<div id="consectetur" class="Report Item">
						Consectetur adipiscing
					</div>
				</div>
			')
			->seeElement('div.Report.Item')
				->ordered()
				->withAttributes([
					'id' =>'loremIpsum', 
					'text' => 'Lorem ipsum'
				])
				->withAttributes([
					'id' =>'consectetur', 
					'text' => 'Consectetur adipiscing'
				])
				->withAttributes([
					'id' =>'dolorSitAmet', 
					'text' => 'Dolor sit amet'
				]);
	}

	/** @test */
	public function it_wont_override_selector_class_with_attribute_class()
	{
		$this->instance
			->setResponse('
				<div class="contents">
					<div id="loremIpsum" class="Report Item">
						Lorem ipsum
					</div>
					<div id="dolorSitAmet" class="Report Item">
						Dolor sit amet
					</div>
					<div id="consectetur" class="Report Item">
						Consectetur adipiscing
					</div>
				</div>
			')
			->seeElement('div.Report')
				->withAttributes([
					'text' => 'Lorem ipsum',
					'class' => 'Item',
				]);
	}
}