<?php

namespace MolnApps\Testing\Response\Value;

class PlainText
{
	private $markup;
	private $originalText;
	private $text;

	public function __construct($htmlMarkup)
	{
		$this->markup = $htmlMarkup;
		$this->originalText = $htmlMarkup;
		$this->text = $htmlMarkup;
	}

	private function convertMarkupToPlainText($preserveNl = false)
	{
		$this
			->br2nl()
			->plainText()
			->removeTabs()
			->nl2spaces()
			->removeDoubleSpaces()
			->trim();
	}

	public function getText()
	{
		$this->reset();

		$this->convertMarkupToPlainText(false);

		return $this->text;
	}

	public function getTextWithNl()
	{
		$this->reset();

		return $this->originalText;
	}

	private function reset()
	{
		$this->text = $this->originalText;
	}

	private function br2nl()
	{
		$this->text = str_replace(['<br/>', '<br>', '<br />'], "\r\n", $this->text);

		return $this;
	}

	private function plainText()
	{
		$this->text = strip_tags($this->text);
		$this->text = html_entity_decode($this->text, ENT_QUOTES, 'UTF-8');
		
		return $this;
	}

	private function removeTabs()
	{
		$this->text = str_replace(["\t"], '', $this->text);

		return $this;
	}

	private function nl2spaces()
	{
		$this->text = str_replace(["\r", "\n"], ' ', $this->text);

		return $this;
	}

	private function removeDoubleSpaces()
	{
		$this->text = preg_replace('/[\s]+/', ' ', $this->text);
		
		return $this;
	}

	private function trim()
	{
		$this->text = trim($this->text);

		return $this;
	}
}