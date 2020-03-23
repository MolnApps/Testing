<?php

namespace MolnApps\Testing\Response;

use \MolnApps\Testing\TestCase;

use \MolnApps\Testing\Response\Testing\ControllerStub;
use \MolnApps\Testing\Router\Router;

class ResponseInspectorElementChildTest extends TestCase
{
	protected function setUp() : void
	{
		$this->response = '
			<div class="contents">
				<div class="Report Item Collapsable">
					<ul>
						<li class="view">View (2)</li>
						<li class="count"><strong>2</strong></li>
					</ul>
					<p>Lorem ipsum</p>
				</div>
				<div class="Report Item Collapsable">
					<ul>
						<li class="view">View (1)</li>
						<li class="count"><strong>1</strong></li>
					</ul>
					<p>Dolor sit amet</p>
				</div>
				<div class="Report Item Collapsable">
					<ul class="Actions">
						<li class="view">View (0)</li>
						<li class="count"><strong>0</strong></li>
					</ul>
					<p>Consectetur adipiscing</p>
				</div>
			</div>
		';
		$this->instance = new ResponseInspector(new ControllerStub, new Router);

		$this->instance->setResponse($this->response);
	}

	/** @test */
	public function it_asserts_that_a_child_is_found_in_every_element()
	{
		$this->instance
			->seeElement('div.Report.Item')
				->withChild('li.count');
	}

	/** @test */
	public function it_throws_if_a_child_is_not_found_in_every_element()
	{
		$message = 'No element [div.Report.Item] was found with child [li.foobar]';
		$message = 'Element div.Report.Item li.foobar was expected at least 1 time';
		$this->setExpectedException('\Exception', $message);

		$this->instance
			->seeElement('div.Report.Item')
				->withChild('li.foobar');
	}

	/** @test */
	public function it_asserts_that_a_child_with_attribute_is_found()
	{
		$this->instance
			->seeElement('div.Report.Item')
				->withChild('li.count')
					->withAttribute('text', '2');
	}

	/** @test */
	public function it_throws_if_a_child_with_attribute_is_not_found()
	{
		$message = 'No element [div.Report.Item] was found with child [li.count] with attributes [text = "15"]';
		$message = 'Element div.Report.Item li.count[text="15"] was expected at least 1 time';
		$this->setExpectedException('\Exception', $message);

		$this->instance
			->seeElement('div.Report.Item')
				->withChild('li.count')
					->withAttribute('text', '15');
	}

	/** @test */
	public function it_asserts_that_multiple_elements_with_child_with_attribute_are_found()
	{
		$this->instance
			->seeElement('div.Report.Item')
				->withChild('li.count')
					->withAttribute('text', 2)
			->seeElement()
				->withChild('li.count')
					->withAttribute('text', 1)
			->seeElement()
				->withChild('li.count')
					->withAttribute('text', 0);
	}

	/** @test */
	public function it_throws_if_one_child_with_attribute_in_multiple_elements_is_not_found()
	{
		$message = 'No element [div.Report.Item] was found with child [li.count] with attributes [text = "15"]';
		$message = 'Element div.Report.Item li.count[text="15"] was expected at least 1 time';
		$this->setExpectedException('\Exception', $message);

		$this->instance
			->seeElement('div.Report.Item')
				->withChild('li.count')
			->seeElement()
				->withChild()->withAttribute('text', '2')
			->seeElement()
				->withChild()->withAttribute('text', '1')
			->seeElement()
				->withChild()->withAttribute('text', '15');
	}

	/** @test */
	public function it_throws_if_one_child_with_attribute_within_multiple_elements_is_not_found()
	{
		$message = 'No element [div.Report.Item] was found with child [li.count] with attributes [text = "0"]';
		$message = 'Element div.Report.Item li.count[text="0"] was expected at least 1 time';
		$this->setExpectedException('\Exception', $message);

		$this->instance
			->setResponse('
				<div class="contents">
					<div class="Report Item">
						<ul><li class="count">2</li></ul>
						<p>Lorem ipsum</p>
					</div>
					<div class="Report Item">
						<ul><li class="count">1</li></ul>
						<p>Dolor sit amet</p>
					</div>
					<div class="AnotherClass">
						<ul><li class="count">0</li></ul>
						<p>Consectetur adipiscing</p>
					</div>
				</div>
			')
			->seeElement('div.Report.Item')
				->withChild('li.count')
			->seeElement()
				->withChild()
					->withAttribute('text', '2')
			->seeElement()
				->withChild()
					->withAttribute('text', '1')
			->seeElement()
				->withChild()
					->withAttribute('text', '0');
	}

	/** @test */
	public function it_asserts_that_an_element_with_attribute_and_child_is_found()
	{
		$this->instance
			->seeElement('div.Report.Item')
				->withAttribute('text', 'Lorem ipsum')
				->withChild('li.count')
					->withAttribute('text', '2');
	}

	/** @test */
	public function it_asserts_that_multiple_elements_with_attribute_and_child_are_found()
	{
		$this->instance
			->seeElement('div.Report.Item')
				->times(3)
				->withChild('li.count')
			->seeElement()
				->withAttribute('text', 'Lorem ipsum')
				->withChild()->withAttribute('text', '2')
			->seeElement()
				->withAttribute('text', 'Dolor sit amet')
				->withChild()->withAttribute('text', '1')
			->seeElement()
				->withAttribute('text', 'Consectetur adipiscing')
				->withChild()->withAttribute('text', '0');
	}

	/** @test */
	public function it_throws_if_one_in_multiple_elements_with_attribute_and_child_is_not_found()
	{
		$message = 'No current element [div.Report.Item] was found with child [li.count] with attributes [text = "2"]';
		$message = 'Element div.Report.Item[text="Dolor sit amet"] li.count[text="2"] was expected at least 1 time';
		$this->setExpectedException('\Exception', $message);
		
		$this->instance
			->seeElement('div.Report.Item')
				->times(3)
				->withChild('li.count')
			->seeElement()
				->withAttribute('text', 'Lorem ipsum')
				->withChild()->withAttribute('text', '2')
			->seeElement()
				->withAttribute('text', 'Dolor sit amet')
				->withChild()->withAttribute('text', '2')
			->seeElement()
				->withAttribute('text', 'Consectetur adipiscing')
				->withChild()->withAttribute('text', '0');
	}

	/** @test */
	public function it_throws_if_an_element_with_attribute_and_child_is_not_found()
	{
		$message = 'No current element [div.Report.Item] was found with child [li.count] with attributes [text = "2"]';
		$message = 'Element div.Report.Item[text="Dolor sit amet"] li.count[text="2"] was expected at least 1 time';
		$this->setExpectedException('\Exception', $message);

		$this->instance
			->seeElement('div.Report.Item')
				->withAttribute('text', 'Dolor sit amet')
				->withChild('li.count')
					->withAttribute('text', '2');
	}

	/** @test */
	public function it_throws_if_an_element_with_attribute_and_child_is_not_found_2()
	{
		$message = 'No current element [div.Report.Item] was found with child [li.count] with attributes [text = "2"]';
		$message = 'Element div.Report.Item[text="Dolor sit amet"] li.count[text="2"] was expected at least 1 time';
		$this->setExpectedException('\Exception', $message);

		$this->instance
			->setResponse('
				<div class="contents">
					<div class="Report Item">
						<ul><li class="count">2</li></ul>
						<p>Lorem ipsum</p>
					</div>
					<div class="Report Item">
						<ul><li class="count">1</li></ul>
						<p>Dolor sit amet 2</p>
					</div>
					<div class="Report Item">
						<ul><li class="count">0</li></ul>
						<p>Consectetur adipiscing</p>
					</div>
				</div>
			')
			->seeElement('div.Report.Item')
				->withAttribute('text', 'Dolor sit amet')
				->withChild('li.count')
					->withAttribute('text', '2');
	}

	/** @test */
	public function it_asserts_that_multiple_childs_with_different_attributes_are_found()
	{
		$this->instance
			->setResponse('
				<div class="contents">
					<div class="ticket">
						<h3><a href="index.php?cmd=ViewTicket&ticketId=4">Ticket #4</a></h3>
						<span class="topic">aperiam magnam</span><br/>
						<span class="status">Solved</span><br/>
						<p class="details">
							<span class="name">Ludie Runolfsson</span>
						</p>
					</div>
					<div class="ticket">
						<h3><a href="index.php?cmd=ViewTicket&ticketId=4">Ticket #5</a></h3>
						<span class="topic">lorem ipsum</span><br/>
						<span class="status">Solved</span><br/>
						<p class="details">
							<span class="name">Ludie Runolfsson</span>
						</p>
					</div>
				</div>
			')
			->seeElement('div.ticket')
				->withChild('h3')->withAttribute('text', 'Ticket #4')
				->withChild('span.topic')->withAttribute('text', 'aperiam magnam')
				->withChild('span.name')->withAttribute('text', 'Ludie Runolfsson');
	}

	/** @test */
	public function it_throws_if_multiple_childs_with_different_attributes_are_not_found()
	{
		$message = 'No current element [div.ticket] was found with child [span.topic] with attributes [text = "aperiam magnam"]';
		$message = 'Element div.ticket h3[text="Ticket #5"], div.ticket span.topic[text="aperiam magnam"] was expected at least 1 time';
		$this->setExpectedException('\Exception', $message);

		$this->instance
			->setResponse('
				<div class="contents">
					<div class="ticket">
						<h3><a href="index.php?cmd=ViewTicket&ticketId=4">Ticket #4</a></h3>
						<span class="topic">aperiam magnam</span><br/>
						<span class="status">Solved</span><br/>
						<p class="details">
							<span class="name">Ludie Runolfsson</span>
						</p>
					</div>
					<div class="ticket">
						<h3><a href="index.php?cmd=ViewTicket&ticketId=4">Ticket #5</a></h3>
						<span class="topic">lorem ipsum</span><br/>
						<span class="status">Solved</span><br/>
						<p class="details">
							<span class="name">Ludie Runolfsson</span>
						</p>
					</div>
				</div>
			')
			->seeElement('div.ticket')
				->withChild('h3')->withAttribute('text', 'Ticket #5')
				->withChild('span.topic')->withAttribute('text', 'aperiam magnam')
				->withChild('span.name')->withAttribute('text', 'Ludie Runolfsson');
	}

	/** @test */
	public function it_asserts_that_an_empty_child_has_no_text()
	{
		$this->instance
			->setResponse('
				<div class="contents">
					<div class="ticket">
						<h3><a href="index.php?cmd=ViewTicket&ticketId=4">Ticket #4</a></h3>
						<p class="excerpt"></p>
						<span class="topic">aperiam magnam</span><br/>
						<span class="status">Solved</span><br/>
						<p class="details">
							<span class="name">Ludie Runolfsson</span>
						</p>
					</div>
				</div>
			')
			->seeElement('div.ticket')
				->withChild('h3')
					->withText('Ticket #4')
				->withChild('p.excerpt')
					->withText('')
				->withChild('span.topic')
					->withAttributes(['text' => 'aperiam magnam'])
				->withChild('span.status')
					->withText('Solved')
				->withChild('span.name')
					->withText('Ludie Runolfsson');
	}
}