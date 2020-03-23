<?php

namespace MolnApps\Testing\Request;

use \MolnApps\Testing\Response\Html\DomNodeFactory;

use \MolnApps\Testing\Router\Router;

class FormRequestBuilderTest extends \PHPUnit\Framework\TestCase
{
	/** @test */
	public function it_creates_a_request_array()
	{
		$form = $this->createNode('form', ['action' => 'index.php?cmd=MyCommand&confirm=false']);
		
		$form->addChild('input', ['type' => 'text', 'name' => 'firstName', 'value' => 'George']);

		$form->addChild('textarea', ['name' => 'notes', 'text' => 'Lorem ipsum']);

		$select = $this->createNode('select', ['name' => 'country']);
		$select
			->addChild('option', ['value' => 'GB'])
			->addChild('option', ['value' => 'IT', 'selected' => 'selected'])
			->addChild('option', ['value' => 'US']);

		$form->appendChild($select);

		$select = $this->createNode('select', ['name' => 'options']);
		$select
			->addChild('option', ['value' => '0', 'selected' => 'selected'])
			->addChild('option', ['value' => '1'])
			->addChild('option', ['value' => '2']);
		
		$form->appendChild($select);

		$form->addChild('input', ['type' => 'hidden', 'name' => 'meta[1]', 'value' => 'Meta value']);

		$form->addChild('input', ['type' => 'hidden', 'name' => 'src_filters[0][field]', 'value' => 'clientCode']);
		$form->addChild('input', ['type' => 'hidden', 'name' => 'src_filters[0][operator]', 'value' => 'contains']);
		$form->addChild('input', ['type' => 'hidden', 'name' => 'src_filters[0][value]', 'value' => 'foobar']);

		$form->addChild('input', ['type' => 'checkbox', 'name' => 'agree', 'value' => '1', 'checked' => 'checked']);

		$form->addChild('input', ['type' => 'checkbox', 'name' => 'subscribe', 'value' => '1']);

		$form->addChild('input', ['type' => 'hidden', 'name' => 'recordId', 'value' => '12']);

		$builder = new FormRequestBuilder($form);
		$this->assertEquals([
			'cmd' => 'MyCommand', 
			'confirm' => 'false',
			'meta' => [
				'1' => 'Meta value'
			],
			'src_filters' => [
				'0' => [
					'field' => 'clientCode',
					'operator' => 'contains',
					'value' => 'foobar',
				]
			],
			'firstName' => 'George',
			'notes' => 'Lorem ipsum',
			'country' => 'IT',
			'options' => '0',
			'agree' => '1',
			'subscribe' => '',
			'recordId' => '12',
		], $builder->getParams());
	}

	/** @test */
	public function it_will_submit_an_empty_field()
	{
		$form = $this->createNode('form', []);
		$form->addChild('input', ['name' => 'firstName', 'value' => '']);

		$builder = new FormRequestBuilder($form);
		$this->assertEquals([
			'firstName' => '',
		], $builder->getParams());
	}

	/** @test */
	public function it_will_submit_a_form_to_a_router()
	{
		$form = $this->createNode('form', ['action' => '/report/signup']);
		$router = $this->createRouter();

		$builder = FormRequestBuilder::make($form)->withRouter($router);

		$this->assertEquals(['cmd' => 'SignUp'], $builder->getParams());
	}

	/** @test */
	public function it_will_submit_a_form_to_a_router_without_a_prefix()
	{
		$form = $this->createNode('form', ['action' => 'account/create']);
		$router = $this->createRouter();

		$builder = FormRequestBuilder::make($form)->withRouter($router);

		$this->assertEquals(['cmd' => 'CreateAccount'], $builder->getParams());
	}

	private function createNode($tag, array $attributes)
	{
		return DomNodeFactory::createElement($tag, $attributes);
	}

	private function createRouter()
	{
		$router = new Router;
		$router->prefix('report');
		$router->post('signup', '@SignUp');
		$router->post('account/create', '@CreateAccount');
		return $router;
	}
}