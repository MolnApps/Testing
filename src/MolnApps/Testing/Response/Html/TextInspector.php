<?php

namespace MolnApps\Testing\Response\Html;

use \MolnApps\Testing\Response\Value\PlainText;

class TextInspector
{
	private $expectedText = '';
	private $actualText = '';

	public function __construct($expectedText, $actualText)
	{
		$this->expectedText = $this->normalizeText($expectedText);
		$this->actualText = $this->normalizeText($actualText);
	}

	private function normalizeText($text)
	{
		return (new PlainText($text))->getText();
	}

	public function contains()
	{
		return (
			$this->expectedText == $this->actualText || 
			stripos($this->actualText, $this->expectedText) !== false
		);
	}
}